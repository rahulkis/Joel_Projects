<?php

get_header();
do_action( "before_dearpdf_single_content" );

do_action( "dearpdf_single_content" );

do_action( "after_dearpdf_single_content" );
get_footer();
