<?php
/*
Plugin Name: Infinite Scroll helper plugin for Graphene
Plugin URI: http://www.prasannasp.net/wordpress-plugins/infinite-scroll-helper-plugin-for-graphene/
Description: Infinite Scroll helper plugin for the Graphene Theme. JetPack plugin should be installed first.
Author: Prasanna SP
Version: 1.0
Author URI: http://www.prasannasp.net/
*/

// Add theme support for infinite scroll module in the JetPack plugin
add_theme_support( 'infinite-scroll', array(
 	'type'           => 'click',
	'footer_widgets' => false,
	'container'      => 'content-main',
	'wrapper'        => true,
	'render'         => 'infinite_scroll_graphene_render',
	'posts_per_page' => false
) );

// render posts
function infinite_scroll_graphene_render() {
        while ( have_posts() ) {
                the_post();
                get_template_part( 'loop', get_post_format() );
        }
}

// enqueue stylesheet to hide posts navigation when infinite scroll is active
wp_enqueue_style( 'infinite-scroll-graphene', plugins_url( '/css/infinite-scroll-graphene.css' , __FILE__ ), array(), '', false );
