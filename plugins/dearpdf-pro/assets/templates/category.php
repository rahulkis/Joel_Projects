<?php
//Since the custom post is set to "exclude_from_search" Wordpress normal archive search wont display any posts.

get_header();
do_action( "before_dearpdf_category_content" );

do_action( "dearpdf_category_content" );

do_action( "after_dearpdf_category_content" );
get_footer();
