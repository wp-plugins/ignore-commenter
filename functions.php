<?php

/**
 * Returns the current wd_ic_framework version number
 * @return string
 */

function wd_ic_installed_version() {
	return get_option( WD_IC_NAME );  
}

/**
 * Dactivation of WD_IC_Framework
 * These routines handle deactivation of WD_BKNramework.  Deletes/Removes Options. 
 */

function wd_ic_deactivate()
{
	global $wpdb, $wd_ic_options_all;
	delete_option( WD_IC_NAME );	
}


/**
 * Activation of WD_IC_framework
 * Handles activation of plugin.  Creates/Updates 
 * Options will be added only if they don't exist.
 */

function wd_ic_activate() {

	global $wd_ic_options_all;
	if ( wd_ic_installed_version() != WD_IC_VERSION ) {
		add_option( WD_IC_NAME, WD_IC_VERSION );	
	}
}


/**
 * Checks if this plugin is an update from a previous version. This routine 
 * is used in 'wd_ic_framework.php' and is executed every time wordpress calls
 * the 'plugins_loaded' action.
 */

add_action('plugins_loaded', 'wd_ic_check_for_updates' ); 

function wd_ic_check_for_updates() {
	wd_ic_activate();
}


/**
 * Load scripts and styles
 */

function wd_ic_load_scripts_and_styles() { 
    // scripts    
    wp_enqueue_script( 'jquery' );  
}

add_action( 'enqueue_scripts', 'wd_ic_load_scripts_and_styles' );

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );



/**
 * Main Admin Menu
 *
 */

add_action('admin_menu', 'wd_ic_menu');

function wd_ic_menu() {
	add_menu_page('', 'Ignore Comments', 0, WD_IC_NAME, 'studios_ignored_commenter') ;
	add_submenu_page('', 'View Ignored Commenters', 'View', 0, 'ignored-commenters', 'studios_ignored_commenter');
}
 
// Admin menu function
function studios_ignored_commenter() {
	?>
	<div class="wrap">
        <h2>Ignore Commenters</h2>
	</div>
	
	<div class="wrap">
  		<?php
		// set vars
		$meta_key = 'studios-ignore-commenter';
		$my_user_ID = get_current_user_id();
		// do actions
		if($_GET['action'] && $_GET['action'] == 'restore' && $_GET['commenter']) {
			delete_user_meta( $my_user_ID, $meta_key, $_GET['commenter'] );
			echo ('<div class="updated"> <p> User: '. $_GET['commenter'] .' comments are restored.</p></div>');
		}
?>
		<h3>All commenters being ignored:</h3>

<style>
.list-ignored {
    border: 1px solid #ccc;
    display: table;
    line-height: 30px;
    margin: 5px 0;
    padding: 10px;
    width: 390px;
}
</style>		
<?php		
		$ignored = get_user_meta( $my_user_ID, $meta_key );
		if($ignored) {
 			foreach($ignored as $k => $v) { 
			$ignored_ = get_the_author_meta( 'display_name',$v);
		?>	
		<div class="list-ignored">Ignored User: <strong><?php echo $ignored_;?></strong>&nbsp; &nbsp; 
			<a href="<?php echo '?page='.$_REQUEST['page'].'&action=restore&commenter='.$v;?>" class="button">Restore Commenter</a>
		</div>
			<?php } ?>
		<?php
		} else { ?>
		<div class="list-ignored">No Ignored Commenters.</div>
<?php
		}
		?>
	</div>
	<?php
}


/* IGONRE USER functions
 * "ignore" button so that you can click it beside the user you no longer want 
 * to see comments from and you won't see comments from this person on any post/page.
 *
 */
 
// Post action
function studios_ignore_commenter() {
	 
	// listen for post of ignore	
	if($_POST['send-ignore-commenter']) {
 		$author_id = $_POST['send-ignore-commenter'];
 		$user_id = $_POST['send-ignorer'];
		$author = get_user_by( 'id', $author_id );
		echo '{"author": "'.$author->display_name.'",';
 		echo ' "user_ID": "'.$author_id.'",';
		// build vars
		$meta_value = $author_id;
		$meta_key = 'studios-ignore-commenter';
		$ignored = add_user_meta( $user_id, $meta_key, $meta_value );
		if($ignored) 
			echo '"success": true';
		else echo '"success": false';
		echo '}';	
		exit;
	}
 
}  
add_action('init', 'studios_ignore_commenter');

// Ignore Button
function ignore_user_link($user_id) { 
	$my_user_ID = get_current_user_id();
	if($my_user_ID AND $user_id AND ($user_id != $my_user_ID) ) {
	?>
	<p class="ignore-commenter"><a href="#" class="activate" data-ignored-id="<?php echo $user_id;?>" data-ignorer-id="<?php echo $my_user_ID;?>">Ignore Commenter</a></p>
<?php 	
	}
}


// Footer script
function studios_ignore_script() {
	// add sript to comments
	$notice = '<div id="ignore-notice" style=" position: fixed; min-height: 25px; left: 40%; top: 300px; margin: 5px 0 15px; border: #e6db55 2px solid; padding: 10px 10px 5px; background: #ffffe0; border-radius: 7px; box-shadow: 0px 0px 5px #ccc; width: 20%; z-index: 100;">Ignoring comments from User: <strong>\' +ret.author + \'</strong> <a href="#" class="dismiss-notice">Dismiss</a></div>';
	echo '<script>
jQuery(".ignore-commenter .activate").click(function() {
	console.log("activate");
	jQuery.ajax({
		url: "",
		dataType: "json",
		type: "POST",
		data: {"send-ignore-commenter": jQuery(this).data("ignored-id"), "send-ignorer": jQuery(this).data("ignorer-id")},
		success: function(ret) {
			// console.log(ret);
			if(ret.success) {
				jQuery("body").append(\''.$notice.'\');
			}
			// reload after 3 secs
			setTimeout(function() {
				window.location.reload();
			}, 2000);
		}
	});	
	return false;
});
jQuery(".dismiss-notice").live("click",function() {
	jQuery(this).parents("#ignore-notice").remove();	
	return false;
});	
	</script>';
}  
add_action('wp_footer', 'studios_ignore_script');


// Comments Template 
add_filter( "comments_template", "studios_ignore_comment_template" );

function studios_ignore_comment_template( $comment_template ) {
     global $post;
     if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
        return;
     }
     return dirname(__FILE__) . '/comments-template.php';
}


// Comment Filter Function
function studios_custom_comments($comment, $args, $depth) {	
	// Get user
	$user_id=get_current_user_id();
	
	if($user_id) 
	$ignored_users = get_user_meta($user_id, $key='studios-ignore-commenter');
	else 
	$ignored_users = array();	

	if( in_array($comment->user_id,$ignored_users))
	return;
	
	/*** twentytwelve comments theme ***/
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'twentytwelve' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'twentytwelve' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link">', '</p>' ); ?>
				<?php ignore_user_link($comment->user_id); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
	
	
}




