<?php

namespace WP_Defender\Model\Setting;

use Calotes\Model\Setting;

/**
 * Class Global_Ip_Lockout
 *
 * @package WP_Defender\Model\Setting
 */
class Global_Ip_Lockout extends Setting {

	protected $table = 'wd_global_ip_settings';

	/**
	 * @var bool
	 * @defender_property
	 */
	public $enabled = false;

	/**
	 * @var bool
	 * @defender_property
	 */
	public $blocklist_autosync = false;

	/**
	 * Validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
		[ [ 'enabled', 'blocklist_autosync' ], 'boolean' ],
	];

	/**
	 * Define settings labels.
	 *
	 * @return array
	 */
	public function labels(): array {
		return [
			'enabled' => self::get_module_name(),
			'blocklist_autosync' => __( 'Permanently Blocked IPs', 'wpdef' ),
		];
	}

	public static function get_module_name(): string {
		return __( 'Global IP Blocker', 'wpdef' );
	}
}