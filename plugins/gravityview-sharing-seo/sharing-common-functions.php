<?php

if( ! function_exists('gravityview_social_get_title') ) {

	/**
	 * Get the title to be used for the social sharing. Can be overridden.
	 *
	 * @uses GravityView_frontend::getEntry()
	 * @uses GravityView_API::replace_variables()
	 * @uses GravityView_frontend::get_context_view_id()
	 *
	 * @since 3.2 Added $view parameter.
	 *
	 * @param $previous_title
	 * @param int $post_id
	 * @param \GV\View|null $view
	 *
	 * @return string
	 */
	function gravityview_social_get_title( $previous_title, $post_id = 0, $view = null ) {

		$gv_entry = gravityview()->request->is_entry();

		if ( $gv_entry instanceof \GV\Entry ) {
			$entry = $gv_entry->as_entry();
		} else {
			$entry = array();
		}

		if( $view instanceof \GV\View && $view->settings ) {
			return \GravityView_API::replace_variables( $view->settings->get( 'single_title', '' ), $view->form->form, $entry );
		}

		// TODO: Replace this with gravityview()->views->get() if $view isn't passed.
		$view_id = GravityView_frontend::getInstance()->get_context_view_id();

		$data = gravityview_get_current_view_data( $view_id );

		if ( ! empty( $data ) ) {

			$single_entry_title_setting = trim( rtrim( $data['atts']['single_title'] ) );

			if ( ! empty( $single_entry_title_setting ) ) {
				return \GravityView_API::replace_variables( $single_entry_title_setting, $data['form'], $entry );
			}
		}

		return $previous_title;
	}
}

if( ! function_exists('gravityview_social_get_permalink') ) {

	/**
	 * Filter the permalink for a post with a custom post type.
	 *
	 * @param string $post_link The post's permalink.
	 * @param WP_Post|null $passed_post The post in question.
	 * @param bool $leavename Whether to keep the post name.
	 * @param bool $sample Is it a sample permalink.
	 */
	function gravityview_social_get_permalink( $post_link, $passed_post = NULL, $leavename = false, $sample = false ) {
		global $post;

		if ( is_admin() || ! function_exists( 'gravityview' ) ) {
			return $post_link;
		}

		if ( $passed_post instanceof WP_Post ) {
			$post_id = $passed_post->ID;
		} elseif ( $passed_post ) {
			$post_id = $passed_post;
		} elseif ( $post instanceof WP_Post ) {
			$post_id = $post->ID;
		} else {
			return $post_link;
		}

		$instance = GravityView_Sharing_Service::getInstance();

		// Prevent loop
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		$trace = wp_list_pluck( $trace, 'function' );
		array_shift( $trace );
		if ( in_array( __FUNCTION__, $trace ) ) {
			return $post_link;
		}

		if ( $instance && $instance::$context && $instance::$context->entry ) {
			return $instance::$context->entry->get_permalink();
		} else if ( $entry = gravityview()->request->is_entry() ) {
			return $entry->get_permalink();
		}

		return $post_link;
	}

}
