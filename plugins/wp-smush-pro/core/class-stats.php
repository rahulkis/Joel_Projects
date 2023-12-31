<?php
/**
 * Class that is responsible for all stats calculations.
 *
 * @since 3.4.0
 * @package Smush\Core
 */

namespace Smush\Core;

use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Query;
use Smush\Core\Png2Jpg\Png2Jpg_Optimization;
use Smush\Core\Resize\Resize_Optimization;
use Smush\Core\Smush\Smush_Optimization;
use Smush\Core\Smush\Smush_Optimization_Global_Stats;
use Smush\Core\Stats\Global_Stats;
use stdClass;
use WP_Query;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Stats
 */
class Stats {

	/**
	 * Stores the stats for all the images.
	 *
	 * @var array $stats
	 */
	public $stats;

	/**
	 * Compressed attachments from selected directories.
	 *
	 * @var array $dir_stats
	 */
	public $dir_stats;

	/**
	 * Set a limit of MySQL query. Default: 3000.
	 *
	 * @var int $query_limit
	 */
	private $query_limit;

	/**
	 * Set a limit to max number of rows in MySQL query. Default: 5000.
	 *
	 * @var int $max_rows
	 */
	private $max_rows;

	/**
	 * Attachment IDs.
	 *
	 * @var array $attachments
	 */
	public $attachments = array();

	/**
	 * Image ids that needs to be resmushed.
	 *
	 * @var array $resmush_ids
	 */
	public $resmush_ids = array();

	/**
	 * Percentage of the smushed images.
	 *
	 * @var float
	 */
	public $percent_optimized;

	/**
	 * Percentage metric.
	 *
	 * @var float
	 */
	public $percent_metric;

	/**
	 * Class name of grade type.
	 *
	 * @var string
	 */
	public $percent_grade;

	/**
	 * Protected init class, used in child methods instead of constructor.
	 *
	 * @since 3.4.0
	 */
	protected function init() {}

	/**
	 * Stats constructor.
	 */
	public function __construct() {
		$this->init();

		$this->query_limit = apply_filters( 'wp_smush_query_limit', 3000 );
		$this->max_rows    = apply_filters( 'wp_smush_max_rows', 5000 );

		// Recalculate resize savings.
		add_action(
			'wp_smush_image_resized',
			function() {
				return $this->get_savings( 'resize' );
			}
		);

		// Update Conversion savings.
		add_action(
			'wp_smush_png_jpg_converted',
			function() {
				return $this->get_savings( 'pngjpg' );
			}
		);

		// Update the media_attachments list.
		add_action( 'add_attachment', array( $this, 'add_to_media_attachments_list' ) );
		add_action( 'delete_attachment', array( $this, 'update_lists' ), 12 );
	}

	/**
	 * Runs the expensive queries to get our global smush stats
	 *
	 * @param bool $force_update  Whether to force update the global stats or not.
	 */
	public function setup_global_stats( $force_update = false ) {
		if ( ! $this->mod->dir ) {
			$this->mod->dir = new Modules\Dir();
		}

		// Set directory smush status.
		$this->dir_stats = Modules\Dir::should_continue() ? $this->mod->dir->total_stats() : array();

		// Set Attachment IDs, and total count.
		$this->attachments = $this->get_media_attachments();

		// Set total count.
		$this->total_count = ! empty( $this->attachments ) && is_array( $this->attachments ) ? count( $this->attachments ) : 0;

		$this->stats = $this->global_stats( $force_update );

		if ( empty( $this->smushed_attachments ) ) {
			// Get smushed attachments.
			$this->smushed_attachments = $this->get_smushed_attachments( $force_update );
		}

		// Get super smushed images count.
		if ( ! $this->super_smushed ) {
			$this->super_smushed = count( $this->get_super_smushed_attachments() );
		}

		// Get skipped attachments.
		$this->skipped_attachments = $this->skipped_count( $force_update );
		$this->skipped_count       = count( $this->skipped_attachments );

		// Set smushed count.
		$this->smushed_count   = ! empty( $this->smushed_attachments ) ? count( $this->smushed_attachments ) : 0;
		$this->remaining_count = $this->remaining_count();

		list( $percent_optimized, $percent_metric, $grade ) = $this->get_grade_data(
			$this->remaining_count,
			$this->total_count,
			$this->skipped_count
		);
		$this->percent_grade     = $grade;
		$this->percent_metric    = $percent_metric;
		$this->percent_optimized = $percent_optimized;
	}

	/**
	 * Get the savings from image resizing or PNG -> JPG conversion savings.
	 *
	 * @param string $type          Savings type. Accepts: resize, pngjpg.
	 * @param bool   $force_update  Force update to re-calculate all stats. Default: false.
	 * @param bool   $format        Format the bytes in readable format. Default: false.
	 * @param bool   $return_count  Return the resized image count. Default: false.
	 *
	 * @return int|array
	 */
	public function get_savings( $type, $force_update = true, $format = false, $return_count = false ) {
		$key       = 'wp-smush-' . $type . '_savings';
		$key_count = 'wp-smush-resize_count';

		if ( ! $force_update ) {
			$savings = wp_cache_get( $key, 'wp-smush' );
			if ( ! $return_count && $savings ) {
				return $savings;
			}

			$count = wp_cache_get( $key_count, 'wp-smush' );
			if ( $return_count && false !== $count ) {
				return $count;
			}
		}

		// If savings or resize image count is not stored in db, recalculate.
		$count      = 0;
		$offset     = 0;
		$query_next = true;

		$savings = array(
			'resize' => array(
				'bytes'       => 0,
				'size_before' => 0,
				'size_after'  => 0,
			),
			'pngjpg' => array(
				'bytes'       => 0,
				'size_before' => 0,
				'size_after'  => 0,
			),
		);

		global $wpdb;

		while ( $query_next ) {
			$query_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key=%s LIMIT %d, %d",
					$key,
					$offset,
					$this->query_limit
				)
			); // Db call ok.

			// No results - break out of loop.
			if ( empty( $query_data ) ) {
				break;
			}

			foreach ( $query_data as $data ) {
				// Skip resmush IDs.
				if ( ! empty( $this->resmush_ids ) && in_array( $data->post_id, $this->resmush_ids, true ) ) {
					continue;
				}

				$count++;

				if ( empty( $data ) ) {
					continue;
				}

				$meta = maybe_unserialize( $data->meta_value );

				// Resize mete already contains all the stats.
				if ( ! empty( $meta ) && ! empty( $meta['bytes'] ) ) {
					$savings['resize']['bytes']       += $meta['bytes'];
					$savings['resize']['size_before'] += $meta['size_before'];
					$savings['resize']['size_after']  += $meta['size_after'];
				}

				// PNG - JPG conversion meta contains stats by attachment size.
				if ( is_array( $meta ) ) {
					foreach ( $meta as $size ) {
						$savings['pngjpg']['bytes']       += isset( $size['bytes'] ) ? $size['bytes'] : 0;
						$savings['pngjpg']['size_before'] += isset( $size['size_before'] ) ? $size['size_before'] : 0;
						$savings['pngjpg']['size_after']  += isset( $size['size_after'] ) ? $size['size_after'] : 0;
					}
				}
			}

			// Update the offset.
			$offset += $this->query_limit;

			// Compare the offset value to total images.
			$query_next = $this->total_count > $offset;
		}

		if ( $format ) {
			$savings[ $type ]['bytes'] = size_format( $savings[ $type ]['bytes'], 1 );
		}

		wp_cache_set( 'wp-smush-resize_savings', $savings['resize'], 'wp-smush' );
		wp_cache_set( 'wp-smush-pngjpg_savings', $savings['pngjpg'], 'wp-smush' );
		wp_cache_set( $key_count, $count, 'wp-smush' );

		return $return_count ? $count : $savings[ $type ];
	}

	/**
	 * Get the media attachment IDs.
	 *
	 * @param bool $force_update  Force update.
	 *
	 * @return array
	 */
	public function get_media_attachments( $force_update = false ) {
		// Return results from cache.
		if ( ! $force_update ) {
			$posts = wp_cache_get( 'media_attachments', 'wp-smush' );
			if ( $posts ) {
				return $posts;
			}
		}

		// Remove the Filters added by WP Media Folder.
		do_action( 'wp_smush_remove_filters' );

		global $wpdb;

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				sprintf(
					'SELECT ID FROM `%s` WHERE post_type = "attachment" AND post_mime_type IN (%s)',
					$wpdb->posts,
					implode( ',', array_fill( 0, count( Core::$mime_types ), '%s' ) )
				),
				Core::$mime_types
			)
		); // Db call ok.

		// Add the attachments to cache.
		wp_cache_set( 'media_attachments', $posts, 'wp-smush' );

		return $posts;
	}

	/**
	 * Adds the ID of the smushed image to the media_attachments list.
	 *
	 * @since 3.7.1
	 *
	 * @param int $id Attachment's ID.
	 */
	public function add_to_media_attachments_list( $id ) {
		$posts = wp_cache_get( 'media_attachments', 'wp-smush' );

		// Return if there's no list to update.
		if ( ! $posts ) {
			return;
		}

		$mime_type = get_post_mime_type( $id );
		$id_string = (string) $id;

		// Add the ID if the mime type is allowed and the ID isn't in the list already.
		if ( $mime_type && in_array( $mime_type, Core::$mime_types, true ) && ! in_array( $id_string, $posts, true ) ) {
			$posts[] = $id_string;
			wp_cache_set( 'media_attachments', $posts, 'wp-smush' );
		}
	}

	/**
	 * Updates the IDs lists when an attachment is deleted.
	 *
	 * @since 3.7.2
	 *
	 * @param integer $id Deleted attachment ID.
	 */
	public function update_lists( $id ) {
		$this->remove_from_media_attachments_list( $id );
		self::remove_from_smushed_list( $id );
	}

	/**
	 * Removes the ID of the deleted image from the media_attachments list.
	 *
	 * @since 3.7.1
	 *
	 * @param int $id Attachment's ID.
	 */
	private function remove_from_media_attachments_list( $id ) {
		$posts = wp_cache_get( 'media_attachments', 'wp-smush' );

		// Return if there's no list to update.
		if ( ! $posts ) {
			return;
		}

		$index = array_search( (string) $id, $posts, true );
		if ( false !== $index ) {
			unset( $posts[ $index ] );
			wp_cache_set( 'media_attachments', $posts, 'wp-smush' );
		}
	}

	/**
	 * Optimised image IDs.
	 *
	 * @param bool $force_update  Force update.
	 *
	 * @return array
	 */
	public function get_smushed_attachments( $force_update = false ) {
		// Remove the Filters added by WP Media Folder.
		do_action( 'wp_smush_remove_filters' );

		global $wpdb;

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s",
				Modules\Smush::$smushed_meta_key
			)
		);

		return $posts;
	}

	/**
	 * Adds an ID to the smushed IDs list from the object cache.
	 *
	 * @since 3.7.2
	 *
	 * @param integer $attachment_id ID of the smushed attachment.
	 */
	public static function add_to_smushed_list( $attachment_id ) {
		$smushed_ids = wp_cache_get( 'wp-smush-smushed_ids', 'wp-smush' );

		if ( ! empty( $smushed_ids ) ) {
			$attachment_id = strval( $attachment_id );

			if ( ! in_array( $attachment_id, $smushed_ids, true ) ) {
				$smushed_ids[] = $attachment_id;

				// Set in cache.
				wp_cache_set( 'wp-smush-smushed_ids', $smushed_ids, 'wp-smush' );
			}
		}
	}

	/**
	 * Removes an ID from the smushed IDs list from the object cache.
	 *
	 * @since 3.7.2
	 *
	 * @param integer $attachment_id ID of the smushed attachment.
	 */
	public static function remove_from_smushed_list( $attachment_id ) {
		$smushed_ids = wp_cache_get( 'wp-smush-smushed_ids', 'wp-smush' );

		if ( ! empty( $smushed_ids ) ) {
			$index = array_search( strval( $attachment_id ), $smushed_ids, true );
			if ( false !== $index ) {
				unset( $smushed_ids[ $index ] );
				wp_cache_set( 'wp-smush-smushed_ids', $smushed_ids, 'wp-smush' );
			}
		}
	}

	/**
	 * Get all the attachments with wp-smush-lossy.
	 *
	 * @return array
	 */
	public function get_super_smushed_attachments() {
		$meta_query = array(
			array(
				'key'   => 'wp-smush-lossy',
				'value' => 1,
			),
		);

		return $this->run_query( $meta_query );
	}

	/**
	 * Fetch all the unsmushed attachments.
	 *
	 * @return array
	 */
	public function get_unsmushed_attachments() {
		return $this->run_query( self::get_unsmushed_meta_query() );
	}

	/**
	 * Temporary remove Smush metadata.
	 *
	 * We use this in order to temporary remove the stats metadata,
	 * e.g While generating thumbnail or wp_generate_ when disabled auto smush.
	 *
	 * Note, if member's site allows compression of the original file,
	 * when we remove stats, we might lose a large amount of storage (stats) that we saved for the member's site.
	 * => TODO: Delete stats or just update new stats with re-smush?
	 *
	 * @since 3.9.6
	 *
	 * @param int $attachment_id    Attachment ID.
	 */
	public function remove_stats( $attachment_id ) {
		// Main stats.
		delete_post_meta( $attachment_id, Modules\Smush::$smushed_meta_key );
		// Lossy flag.
		delete_post_meta( $attachment_id, 'wp-smush-lossy' );
		// Finally, remove the attachment ID from cache.
		self::remove_from_smushed_list( $attachment_id );
	}

	/**
	 * Get unsmushed meta query.
	 *
	 * @return array
	 */
	public static function get_unsmushed_meta_query() {
		$unsmushed_query = array(
			'relation' => 'AND',
			array(
				'key'     => Smush_Optimization::SMUSH_META_KEY,
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => Media_Item::IGNORED_META_KEY,
				'compare' => 'NOT EXISTS',
			),
		);

		return $unsmushed_query;
	}

	/**
	 * Wrapper function for looping over a set of posts and fetching the required, based on the arguments.
	 *
	 * @since 3.8.0  Moved out of get_attachments() and get_super_smushed_attachments().
	 *
	 * @param array $meta_query  Meta query arguments for WP_Query.
	 *
	 * @return array
	 */
	private function run_query( $meta_query = array() ) {
		$get_posts   = true;
		$attachments = array();

		$args = array(
			'fields'                 => array( 'ids', 'post_mime_type' ),
			'post_type'              => 'attachment',
			'post_status'            => 'any',
			'orderby'                => 'ID',
			'order'                  => 'DESC',
			'posts_per_page'         => $this->query_limit,
			'offset'                 => 0,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
			'meta_query'             => $meta_query,
		);

		// Loop over to get all the attachments.
		while ( $get_posts ) {
			// Remove the Filters added by WP Media Folder.
			do_action( 'wp_smush_remove_filters' );

			$query = new WP_Query( $args );

			if ( ! empty( $query->post_count ) && count( $query->posts ) > 0 ) {
				// Get a filtered list of post ids.
				$posts = Helper::filter_by_mime( $query->posts );
				// Merge the results.
				$attachments = array_merge( $attachments, $posts );

				// Update the offset.
				$args['offset'] += $this->query_limit;
			} else {
				// If we didn't get any posts from query, set $get_posts to false.
				$get_posts = false;
			}

			// If we already got enough posts.
			if ( count( $attachments ) >= $this->max_rows ) {
				$get_posts = false;
			} elseif ( ! empty( $this->total_count ) && $this->total_count <= $args['offset'] ) {
				// If total Count is set, and it is already lesser than offset, don't query.
				$get_posts = false;
			}
		}

		// Remove resmush IDs from the list.
		if ( ! empty( $this->resmush_ids ) && is_array( $this->resmush_ids ) ) {
			$attachments = array_diff( $attachments, $this->resmush_ids );
		}

		return $attachments;
	}

	/**
	 * Get the savings for the given set of attachments
	 *
	 * @param array $attachments  Array of attachment IDs.
	 *
	 * @return array Stats
	 *  array(
	 *     'size_before'        => 0,
	 *     'size_after'         => 0,
	 *     'savings_resize'     => 0,
	 *     'savings_conversion' => 0
	 *  )
	 */
	public function get_stats_for_attachments( $attachments = array() ) {
		$stats = array(
			'size_before'        => 0,
			'size_after'         => 0,
			'savings_resize'     => 0,
			'savings_conversion' => 0,
			'count_images'       => 0,
			'count_supersmushed' => 0,
			'count_smushed'      => 0,
			'count_resize'       => 0,
			'count_remaining'    => 0,
		);

		// If we don't have any attachments, return empty array.
		if ( empty( $attachments ) || ! is_array( $attachments ) ) {
			return $stats;
		}

		// Loop over all the attachments to get the cumulative savings.
		foreach ( $attachments as $attachment ) {
			$smush_stats        = get_post_meta( $attachment, Modules\Smush::$smushed_meta_key, true );
			$resize_savings     = get_post_meta( $attachment, 'wp-smush-resize_savings', true );
			$conversion_savings = Helper::get_pngjpg_savings( $attachment );

			if ( ! empty( $smush_stats['stats'] ) ) {
				// Combine all the stats, and keep the resize and send conversion settings separately.
				$stats['size_before'] += ! empty( $smush_stats['stats']['size_before'] ) ? $smush_stats['stats']['size_before'] : 0;
				$stats['size_after']  += ! empty( $smush_stats['stats']['size_after'] ) ? $smush_stats['stats']['size_after'] : 0;
			}

			$stats['count_images'] = 0;
			if ( isset( $smush_stats['sizes'] ) && is_array( $smush_stats['sizes'] ) ) {
				foreach ( $smush_stats['sizes'] as $image_stats ) {
					$stats['count_images'] += $image_stats->size_before !== $image_stats->size_after ? 1 : 0;
				}
			}

			$stats['count_supersmushed'] += ! empty( $smush_stats['stats'] ) && $smush_stats['stats']['lossy'] ? 1 : 0;

			// Add resize saving stats.
			if ( ! empty( $resize_savings ) ) {
				// Add resize and conversion savings.
				$stats['savings_resize'] += ! empty( $resize_savings['bytes'] ) ? $resize_savings['bytes'] : 0;
				$stats['size_before']    += ! empty( $resize_savings['size_before'] ) ? $resize_savings['size_before'] : 0;
				$stats['size_after']     += ! empty( $resize_savings['size_after'] ) ? $resize_savings['size_after'] : 0;
				$stats['count_resize']   += 1;
			}

			// Add conversion saving stats.
			if ( ! empty( $conversion_savings ) ) {
				// Add resize and conversion savings.
				$stats['savings_conversion'] += ! empty( $conversion_savings['bytes'] ) ? $conversion_savings['bytes'] : 0;
				$stats['size_before']        += ! empty( $conversion_savings['size_before'] ) ? $conversion_savings['size_before'] : 0;
				$stats['size_after']         += ! empty( $conversion_savings['size_after'] ) ? $conversion_savings['size_after'] : 0;
			}
			$stats['count_smushed'] += 1;
		}

		return $stats;
	}

	/**
	 * Smush and Resizing Stats Combined together.
	 *
	 * @param array $smush_stats     Smush stats.
	 * @param array $resize_savings  Resize savings.
	 *
	 * @return array Array of all the stats
	 */
	public function combined_stats( $smush_stats, $resize_savings ) {
		if ( empty( $smush_stats ) || empty( $resize_savings ) ) {
			return $smush_stats;
		}

		// Initialize key full if not there already.
		if ( ! isset( $smush_stats['sizes']['full'] ) ) {
			$smush_stats['sizes']['full']              = new stdClass();
			$smush_stats['sizes']['full']->bytes       = 0;
			$smush_stats['sizes']['full']->size_before = 0;
			$smush_stats['sizes']['full']->size_after  = 0;
			$smush_stats['sizes']['full']->percent     = 0;
		}

		// Full Image.
		if ( ! empty( $smush_stats['sizes']['full'] ) ) {
			$smush_stats['sizes']['full']->bytes       = ! empty( $resize_savings['bytes'] ) ? $smush_stats['sizes']['full']->bytes + $resize_savings['bytes'] : $smush_stats['sizes']['full']->bytes;
			$smush_stats['sizes']['full']->size_before = ! empty( $resize_savings['size_before'] ) && ( $resize_savings['size_before'] > $smush_stats['sizes']['full']->size_before ) ? $resize_savings['size_before'] : $smush_stats['sizes']['full']->size_before;
			$smush_stats['sizes']['full']->percent     = ! empty( $smush_stats['sizes']['full']->bytes ) && $smush_stats['sizes']['full']->size_before > 0 ? ( $smush_stats['sizes']['full']->bytes / $smush_stats['sizes']['full']->size_before ) * 100 : $smush_stats['sizes']['full']->percent;

			$smush_stats['sizes']['full']->size_after = $smush_stats['sizes']['full']->size_before - $smush_stats['sizes']['full']->bytes;

			$smush_stats['sizes']['full']->percent = round( $smush_stats['sizes']['full']->percent, 1 );
		}

		return $this->total_compression( $smush_stats );
	}

	/**
	 * Combine Savings from PNG to JPG conversion with smush stats
	 *
	 * @param array $stats               Savings from Smushing the image.
	 * @param array $conversion_savings  Savings from converting the PNG to JPG.
	 *
	 * @return Object|array Total Savings
	 */
	public function combine_conversion_stats( $stats, $conversion_savings ) {
		if ( empty( $stats ) || empty( $conversion_savings ) ) {
			return $stats;
		}

		foreach ( $conversion_savings as $size_k => $savings ) {
			// Initialize Object for size.
			if ( empty( $stats['sizes'][ $size_k ] ) ) {
				$stats['sizes'][ $size_k ]              = new stdClass();
				$stats['sizes'][ $size_k ]->bytes       = 0;
				$stats['sizes'][ $size_k ]->size_before = 0;
				$stats['sizes'][ $size_k ]->size_after  = 0;
				$stats['sizes'][ $size_k ]->percent     = 0;
			}

			if ( ! empty( $stats['sizes'][ $size_k ] ) && ! empty( $savings ) ) {
				$stats['sizes'][ $size_k ]->bytes       = $stats['sizes'][ $size_k ]->bytes + $savings['bytes'];
				$stats['sizes'][ $size_k ]->size_before = $stats['sizes'][ $size_k ]->size_before > $savings['size_before'] ? $stats['sizes'][ $size_k ]->size_before : $savings['size_before'];
				$stats['sizes'][ $size_k ]->percent     = ! empty( $stats['sizes'][ $size_k ]->bytes ) && $stats['sizes'][ $size_k ]->size_before > 0 ? ( $stats['sizes'][ $size_k ]->bytes / $stats['sizes'][ $size_k ]->size_before ) * 100 : $stats['sizes'][ $size_k ]->percent;
				$stats['sizes'][ $size_k ]->percent     = round( $stats['sizes'][ $size_k ]->percent, 1 );
			}
		}

		return $this->total_compression( $stats );
	}

	/**
	 * Iterate over all the size stats and calculate the total stats
	 *
	 * @param array $stats  Stats array.
	 *
	 * @return mixed
	 */
	public function total_compression( $stats ) {
		$stats['stats']['size_before'] = 0;
		$stats['stats']['size_after']  = 0;
		$stats['stats']['time']        = 0;

		foreach ( $stats['sizes'] as $size_stats ) {
			$stats['stats']['size_before'] += ! empty( $size_stats->size_before ) ? $size_stats->size_before : 0;
			$stats['stats']['size_after']  += ! empty( $size_stats->size_after ) ? $size_stats->size_after : 0;
			$stats['stats']['time']        += ! empty( $size_stats->time ) ? $size_stats->time : 0;
		}

		$stats['stats']['bytes'] = ! empty( $stats['stats']['size_before'] ) && $stats['stats']['size_before'] > $stats['stats']['size_after'] ? $stats['stats']['size_before'] - $stats['stats']['size_after'] : 0;

		if ( ! empty( $stats['stats']['bytes'] ) && ! empty( $stats['stats']['size_before'] ) ) {
			$stats['stats']['percent'] = ( $stats['stats']['bytes'] / $stats['stats']['size_before'] ) * 100;
		}

		return $stats;
	}

	/**
	 * Get all the attachment meta, sum up the stats and return
	 *
	 * @param bool $force_update     Whether to forcefully update the cache.
	 *
	 * @return array|bool|mixed
	 */
	private function global_stats( $force_update = false ) {
		$stats = get_option( 'smush_global_stats' );

		// Remove id from global stats stored in db.
		if ( ! $force_update && ! empty( $stats ) && isset( $stats['size_before'] ) ) {
			if ( isset( $stats['id'] ) ) {
				unset( $stats['id'] );
			}

			return $stats;
		}

		global $wpdb;

		$smush_data = array(
			'size_before'  => 0,
			'size_after'   => 0,
			'percent'      => 0,
			'human'        => 0,
			'bytes'        => 0,
			'total_images' => 0,
		);

		$offset       = 0;
		$supersmushed = 0;
		$query_next   = true;

		while ( $query_next ) {
			$global_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key=%s GROUP BY post_id LIMIT %d, %d",
					Modules\Smush::$smushed_meta_key,
					$offset,
					$this->query_limit
				)
			); // Db call ok; no-cache ok.

			// If we didn't got any results.
			if ( ! $global_data ) {
				break;
			}

			foreach ( $global_data as $data ) {
				// Skip attachment, if not in attachment list.
				if ( ! in_array( $data->post_id, $this->attachments, true ) ) {
					continue;
				}

				$smush_data['id'][] = $data->post_id;
				if ( ! empty( $data->meta_value ) ) {
					$meta = maybe_unserialize( $data->meta_value );
					if ( ! empty( $meta['stats'] ) ) {

						// Check for lossy compression.
						if ( true === $meta['stats']['lossy'] ) {
							$supersmushed++;
						}

						// If the image was optimised.
						if ( ! empty( $meta['stats'] ) && $meta['stats']['size_before'] >= $meta['stats']['size_after'] ) {
							// Total Image Smushed.
							$smush_data['total_images'] += ! empty( $meta['sizes'] ) ? count( $meta['sizes'] ) : 0;
							$smush_data['size_before']  += ! empty( $meta['stats']['size_before'] ) ? (int) $meta['stats']['size_before'] : 0;
							$smush_data['size_after']   += ! empty( $meta['stats']['size_after'] ) ? (int) $meta['stats']['size_after'] : 0;
						}
					}
				}
			}

			$smush_data['bytes'] = $smush_data['size_before'] - $smush_data['size_after'];

			// Update the offset.
			$offset += $this->query_limit;

			// Compare the Offset value to total images.
			if ( ! empty( $this->total_count ) && $this->total_count <= $offset ) {
				$query_next = false;
			}
		}

		// Add directory smush image bytes.
		if ( ! empty( $this->dir_stats['bytes'] ) && $this->dir_stats['bytes'] > 0 ) {
			$smush_data['bytes'] += $this->dir_stats['bytes'];
		}
		// Add directory smush image total size.
		if ( ! empty( $this->dir_stats['orig_size'] ) && $this->dir_stats['orig_size'] > 0 ) {
			$smush_data['size_before'] += $this->dir_stats['orig_size'];
		}
		// Add directory smush saved size.
		if ( ! empty( $this->dir_stats['image_size'] ) && $this->dir_stats['image_size'] > 0 ) {
			$smush_data['size_after'] += $this->dir_stats['image_size'];
		}
		// Add directory smushed images.
		if ( ! empty( $this->dir_stats['optimised'] ) && $this->dir_stats['optimised'] > 0 ) {
			$smush_data['total_images'] += $this->dir_stats['optimised'];
		}

		// Resize Savings.
		$smush_data['resize_count']   = $this->get_savings( 'resize', false, false, true );
		$resize_savings               = $this->get_savings( 'resize', false );
		$smush_data['resize_savings'] = ! empty( $resize_savings['bytes'] ) ? $resize_savings['bytes'] : 0;

		// Conversion Savings.
		$conversion_savings               = $this->get_savings( 'pngjpg', false );
		$smush_data['conversion_savings'] = ! empty( $conversion_savings['bytes'] ) ? $conversion_savings['bytes'] : 0;

		if ( ! isset( $smush_data['bytes'] ) || $smush_data['bytes'] < 0 ) {
			$smush_data['bytes'] = 0;
		}

		// Add the resize savings to bytes.
		$smush_data['bytes']       += $smush_data['resize_savings'];
		$smush_data['size_before'] += $resize_savings['size_before'];
		$smush_data['size_after']  += $resize_savings['size_after'];

		// Add Conversion Savings.
		$smush_data['bytes']       += $smush_data['conversion_savings'];
		$smush_data['size_before'] += $conversion_savings['size_before'];
		$smush_data['size_after']  += $conversion_savings['size_after'];

		if ( $smush_data['size_before'] > 0 ) {
			$smush_data['percent'] = ( $smush_data['bytes'] / $smush_data['size_before'] ) * 100;
		}

		// Round off percentage.
		$smush_data['percent'] = round( $smush_data['percent'], 1 );

		// Human-readable format.
		$smush_data['human'] = size_format(
			$smush_data['bytes'],
			( $smush_data['bytes'] >= 1024 ) ? 1 : 0
		);

		// Setup Smushed attachment IDs.
		$this->smushed_attachments = ! empty( $smush_data['id'] ) ? $smush_data['id'] : '';

		// Super Smushed attachment count.
		$this->super_smushed = $supersmushed;

		// Remove ids from stats.
		unset( $smush_data['id'] );

		// Update cache.
		update_option( 'smush_global_stats', $smush_data, false );

		return $smush_data;
	}

	/**
	 * Returns remaining count
	 *
	 * @return int
	 */
	public function remaining_count() {
		$resmush_count   = count( $this->resmush_ids );
		$unsmushed_count = $this->total_count - $this->smushed_count - $this->skipped_count;

		// Just a failsafe - can't have remaining value be a negative value.
		$unsmushed_count = $unsmushed_count > 0 ? $unsmushed_count : 0;

		return $resmush_count + $unsmushed_count;
	}

	/**
	 * Return the number of skipped attachments.
	 *
	 * @since 3.0
	 *
	 * @param bool $force  Force data refresh.
	 *
	 * @return array
	 */
	private function skipped_count( $force ) {
		$images = wp_cache_get( 'skipped_images', 'wp-smush' );
		if ( ! $force && $images ) {
			return $images;
		}

		global $wpdb;
		$ignored_query = "SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key = %s";
		$args[]        = Error_Handler::IGNORE_KEY;

		// Animated files are considered ignored
		$ignored_query .= ' OR meta_key = %s AND meta_value = %s';
		$args[]        = Error_Handler::ERROR_KEY;
		$args[]        = Error_Handler::ANIMATED_ERROR_CODE;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		$images = $wpdb->get_col( $wpdb->prepare( $ignored_query, $args ) );
		wp_cache_set( 'skipped_images', $images, 'wp-smush' );

		return $images;
	}

	/**
	 * Checks every place where ignored or animated flag has been stored in the past to get a count.
	 *
	 * TODO: this is meant to be a temporary method to be used until the new stats are adopted. Remove in a few versions.
	 *
	 * @return int
	 */
	private function get_skipped_count() {
		global $wpdb;

		$animated_key = Media_Item::ANIMATED_META_KEY;
		$ignored_key  = Media_Item::IGNORED_META_KEY;

		$error_meta_key       = Error_Handler::ERROR_KEY;
		$animated_error_value = 'animated';

		$mime_types = ( new Smush_File() )->get_supported_mime_types();
		$mime_types = implode( "','", $mime_types );

		$query = $wpdb->prepare(
			"SELECT COUNT(DISTINCT postmeta.post_id) FROM $wpdb->postmeta as postmeta
                    INNER JOIN $wpdb->posts as posts
                        ON postmeta.post_id = posts.ID AND posts.post_type = 'attachment'
                        AND posts.post_mime_type IN ('". $mime_types ."')
					WHERE meta_key = %s
					OR meta_key = %s
					OR (meta_key = %s AND meta_value = %s)",
			$ignored_key,
			$animated_key,
			$error_meta_key,
			$animated_error_value
		);

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * On sites where the new scan has never been run, this method is meant act as a fallback.
	 *
	 * TODO: this is meant to be a temporary method to be used until the new stats are adopted. Remove in a few versions.
	 *
	 * @return array
	 */
	public function get_backup_global_stats() {
		$backup_stats = wp_cache_get( 'backup_global_stats', 'wp-smush' );
		if ( empty( $backup_stats ) ) {
			$backup_stats = $this->fetch_backup_global_stats();
			wp_cache_set( 'backup_global_stats', $backup_stats, 'wp-smush' );
		}

		return $backup_stats;
	}

	public function fetch_backup_global_stats() {
		$stats                         = get_option( 'smush_global_stats' );
		$array_utils                   = new Array_Utils();
		$savings_percent               = $array_utils->get_array_value( $stats, 'percent' );
		$query                         = new Media_Item_Query();
		$image_attachment_count        = $query->get_image_attachment_count();
		$smushed_count                 = $query->get_smushed_count();
		$skipped_count                 = $this->get_skipped_count();
		$total_optimizable_items_count = $image_attachment_count - $skipped_count;
		$total_optimizable_items_count = $total_optimizable_items_count > 0 ? $total_optimizable_items_count : 0;
		$unsmushed_count               = $image_attachment_count - $smushed_count - $skipped_count;
		$unsmushed_count               = $unsmushed_count > 0 ? $unsmushed_count : 0;
		$resmush_count                 = count( (array) get_option( 'wp-smush-resmush-list', array() ) );
		$remaining_count               = $unsmushed_count + $resmush_count;
		$bytes                         = (int) $array_utils->get_array_value( $stats, 'bytes' );
		$human_bytes                   = size_format(
			$bytes,
			$bytes >= 1024 ? 1 : 0
		);
		$resize_savings                = (int) $array_utils->get_array_value( $stats, 'resize_savings' );
		$resize_savings_human          = size_format(
			$resize_savings,
			$resize_savings >= 1024 ? 1 : 0
		);
		$conversion_savings            = (int) $array_utils->get_array_value( $stats, 'conversion_savings' );
		$conversion_savings_human      = size_format(
			$conversion_savings,
			$conversion_savings >= 1024 ? 1 : 0
		);

		list( $percent_optimized, $percent_metric, $grade ) = $this->get_grade_data( $remaining_count, $image_attachment_count, $skipped_count );

		return array(
			'stats_updated_timestamp' => false,
			'is_outdated'             => true,
			'count_supersmushed'      => $query->get_lossy_count(),
			'count_smushed'           => $smushed_count,
			'count_total'             => $total_optimizable_items_count,
			'count_images'            => (int) $array_utils->get_array_value( $stats, 'total_images' ),
			'count_resize'            => (int) $array_utils->get_array_value( $stats, 'resize_count' ),
			'count_skipped'           => $skipped_count,
			'unsmushed'               => array(),
			'count_unsmushed'         => $unsmushed_count,
			'resmush'                 => array(),
			'count_resmush'           => $resmush_count,
			'size_before'             => (int) $array_utils->get_array_value( $stats, 'size_before' ),
			'size_after'              => (int) $array_utils->get_array_value( $stats, 'size_after' ),
			'savings_bytes'           => $bytes,
			'human_bytes'             => $human_bytes,
			'savings_resize'          => $resize_savings,
			'savings_resize_human'    => $resize_savings_human,
			'savings_conversion'      => $conversion_savings,
			'savings_conversion_human'=> $conversion_savings_human,
			'savings_dir_smush'       => $this->dir_stats,
			'savings_percent'         => $savings_percent > 0 ? number_format_i18n( $savings_percent, 1 ) : 0,
			'percent_grade'           => $grade,
			'percent_metric'          => $percent_metric,
			'percent_optimized'       => $percent_optimized,
			'remaining_count'         => $remaining_count,
		);
	}

	/**
	 * Returns an array that can be consumed by the JS
	 *
	 * TODO: When we have rewritten the frontend of the plugin we can directly use {@see Global_Stats::to_array()} instead
	 *
	 * @return array
	 */
	public function get_global_stats() {
		$global_stats = Global_Stats::get();
		if ( empty( $global_stats->get_stats_update_started_timestamp() ) ) {
			// A scan was never started, use the old stats
			return $this->get_backup_global_stats();
		}

		$total_stats = $global_stats->get_sum_of_optimization_global_stats();
		/**
		 * @var $smush_stats Smush_Optimization_Global_Stats
		 */
		$smush_stats   = $global_stats->get_persistable_stats_for_optimization( Smush_Optimization::KEY )
		                              ->get_stats();
		$resize_stats  = $global_stats->get_persistable_stats_for_optimization( Resize_Optimization::KEY )
		                              ->get_stats();
		$png2jpg_stats = $global_stats->get_persistable_stats_for_optimization( Png2Jpg_Optimization::KEY )
		                              ->get_stats();

		return array(
			'stats_updated_timestamp'  => $global_stats->get_stats_updated_timestamp(),
			'is_outdated'              => $global_stats->is_outdated(),
			'count_supersmushed'       => $smush_stats->get_lossy_count(),
			'count_smushed'            => $smush_stats->get_count(),
			'count_total'              => $global_stats->get_total_optimizable_items_count(),
			'count_images'             => $global_stats->get_optimized_images_count(),
			'count_resize'             => $resize_stats->get_count(),
			'count_skipped'            => $global_stats->get_skipped_count(),
			'unsmushed'                => $global_stats->get_optimize_list()->get_ids(),
			'count_unsmushed'          => $global_stats->get_optimize_list()->get_count(),
			'resmush'                  => $global_stats->get_redo_ids(),
			'count_resmush'            => $global_stats->get_redo_count(),
			'size_before'              => $total_stats->get_size_before(),
			'size_after'               => $total_stats->get_size_after(),
			'savings_bytes'            => $total_stats->get_bytes(),
			'human_bytes'              => $total_stats->get_human_bytes(),
			'savings_resize'           => $resize_stats->get_bytes(),
			'savings_resize_human'     => $resize_stats->get_human_bytes(),
			'savings_conversion'       => $png2jpg_stats->get_bytes(),
			'savings_conversion_human' => $png2jpg_stats->get_human_bytes(),
			'savings_dir_smush'        => $this->dir_stats,
			'savings_percent'          => $total_stats->get_percent() > 0 ? number_format_i18n( $total_stats->get_percent(), 1 ) : 0,
			'percent_grade'            => $global_stats->get_grade_class(),
			'percent_metric'           => $global_stats->get_percent_metric(),
			'percent_optimized'        => $global_stats->get_percent_optimized(),
			'remaining_count'          => $global_stats->get_remaining_count(),
		);
	}

	/**
	 * @return int
	 */
	public function get_query_limit() {
		return $this->query_limit;
	}

	/**
	 * @param int $query_limit
	 */
	public function set_query_limit( $query_limit ) {
		$this->query_limit = $query_limit;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_max_rows() {
		return $this->max_rows;
	}

	/**
	 * @param int $max_rows
	 */
	public function set_max_rows( $max_rows ) {
		$this->max_rows = $max_rows;

		return $this;
	}

	/**
	 * Get grade data (percent optimized and class name) for the score widget in summary meta box.
	 *
	 * @return array
	 * @since 3.12.0 Moved it from Abstract_Summary_Page for reuse.
	 *
	 * @since 3.10.0
	 *
	 */
	public function get_grade_data( $total_images_to_smush, $total_count, $skipped_count ) {
		$total_images    = $total_count - $skipped_count;
		$percent_optimized = 0;
		if ( 0 === $total_images ) {
			$grade = 'sui-grade-dismissed';
		} elseif ( $total_images === $total_images_to_smush ) {
			$grade = 'sui-grade-f';
		} else {
			$percent_optimized = floor( ( $total_images - $total_images_to_smush ) * 100 / $total_images );

			$grade = 'sui-grade-f';
			if ( $percent_optimized >= 60 && $percent_optimized < 90 ) {
				$grade = 'sui-grade-c';
			} elseif ( $percent_optimized >= 90 ) {
				$grade = 'sui-grade-a';
			}
		}

		// Don't let percentage go beyond 100 or less than 0
		if ( $percent_optimized > 100 ) {
			$percent_optimized = 100;
		} elseif ( $percent_optimized < 0 ) {
			$percent_optimized = 0;
		}

		return array(
			$percent_optimized,
			0.0 === (float) $percent_optimized ? 100 : $percent_optimized,
			$grade,
		);
	}

	/**
	 * Get resmush ids.
	 *
	 * @return array
	 */
	public function get_resmush_ids() {
		if ( $this->resmush_ids ) {
			return $this->resmush_ids;
		}
		return (array) get_option( 'wp-smush-resmush-list', array() );
	}
}