<?php
/*
Plugin Name: Ignore Commenter
Plugin URI: http://www.pierrestudios.com/plugin_sites/ignore-commenter/
Description: Adds a button to your blog post to allow users to ignore (or hide) comments from unwanted commenters. It's a good way to limit abuse of your blog post comments.
Version: 1.0
Author: Web Dev Studio
Author URI: http://www.pierrestudios.com/plugin_sites/
License: GPLv2 or later
*/

/*  Copyright 2014  Web Dev Studio  (email : webdev@pierrestudios.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


/**** Sets up Plugin configuration and routing based on names of Plugin folder and files. ***/

// define Plugin constants
define( 'WD_IC_VERSION', "1.0");			
define( 'WD_IC_PURGE_DATA', '1' );		
define( 'WP_ADMIN_PATH', ABSPATH . 'wp-admin/');  
define( 'WD_IC_FILE', basename(__FILE__) );
define( 'WD_IC_FILE_PATH', __FILE__);
define( 'WD_IC_NAME', basename(__FILE__, ".php") );
define( 'WD_IC_DB_NAME', WD_IC_NAME ."_DB" );
define( 'WD_IC_PATH', str_replace( '\\', '/', trailingslashit(dirname(__FILE__)) ) );
define( 'WD_IC_URL', plugins_url('', __FILE__) ); 

require_once( WD_IC_PATH . 'functions.php' );

register_activation_hook(__FILE__,'WD_IC_activate'); 
register_deactivation_hook( __FILE__, 'WD_IC_deactivate' );  

?>