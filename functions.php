<?php

if (!function_exists("blog_style")) {
    function blog_style() {
        wp_enqueue_style('blog', get_stylesheet_directory_uri().'/style.css');
        
    }
    
    add_action('wp_enqueue_scripts','blog_style');
}


require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/kusenabdullah-123/blogs-theme',
	__FILE__,
	'blogs-theme'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');
