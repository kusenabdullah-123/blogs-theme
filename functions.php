<?php

if (!function_exists("blog_style")) {
    function blog_style() {
        wp_enqueue_style('blog', get_stylesheet_directory_uri().'/style.css');
        
    }
    
    add_action('wp_enqueue_scripts','blog_style');
}

add_filter( 'site_transient_update_themes', 'misha_update_themes' );

function misha_update_themes( $transient ) {

	// let's get the theme directory name
	// it will be "misha-theme"
	$stylesheet = get_template();

	// now let's get the theme version
	// but maybe it is better to hardcode it in a constant
	$theme = wp_get_theme();
	$version = $theme->get( 'Version' );


	// connect to a remote server where the update information is stored
	$remote = wp_remote_get(
		'https://blog.kusenadev.my.id/index.json',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept' => 'application/json'
			)
		)
	);

	// do nothing if errors
	if(
		is_wp_error( $remote )
		|| 200 !== wp_remote_retrieve_response_code( $remote )
		|| empty( wp_remote_retrieve_body( $remote ) )
	) {
		return $transient;
	}

	// encode the response body
	$remote = json_decode( wp_remote_retrieve_body( $remote ) );
	
	if( ! $remote ) {
		return $transient; // who knows, maybe JSON is not valid
	}
	
	$data = array(
		'theme' => $stylesheet,
		'url' => $remote->details_url,
		'requires' => $remote->requires,
		'requires_php' => $remote->requires_php,
		'new_version' => $remote->version,
		'package' => $remote->download_url,
	);

	// check all the versions now
	if(
		$remote
		&& version_compare( $version, $remote->version, '<' )
		&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )
		&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
	) {

		$transient->response[ $stylesheet ] = $data;

	} else {

		$transient->no_update[ $stylesheet ] = $data;

	}

	return $transient;

}
