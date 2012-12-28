<?php
/*
Plugin Name: Radiostation info management plugin
Version: 0.1
Description: Shoutcast-radiostation info management plugin
*/

//**********************************************************************************************
// **************************** CUSTOM POST TYPE --- STREAM --- ********************************
//**********************************************************************************************
add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'stream',
		array(
			'labels' => array(
				'name' => __( 'Streams' ),
				'singular_name' => __( 'Streams' ),
				'add_new' => _x('Add New', 'stream'),
				'add_new_item' => __('Add New Stream'),
				'edit_item' => __('Edit Stream'),
				'new_item' => __('New Stream'),
				'all_items' => __('All Streams'),
				'view_item' => __('View Stream'),
				'search_items' => __('Search Streams'),
				'not_found' =>  __('No streams found'),
				'not_found_in_trash' => __('No streams found in Trash'), 
				'menu_name' => 'Streams'
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'streams'),
			'menu_position' => 80
		)
	);
	add_post_type_support('stream',array('thumbnail'));
}

/* Define the custom box */

add_action( 'add_meta_boxes', 'stream_add_custom_box' );

// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'stream_add_custom_box', 1 );

/* Do something with the data entered */
add_action( 'save_post', 'stream_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function stream_add_custom_box() {
    add_meta_box(
        'stream_sectionid',
        __( 'Stream URL\'s', 'stream_textdomain' ), 
        'stream_inner_custom_box',
        'stream'
    );
}

/* Prints the box content */
function stream_inner_custom_box( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'stream_noncename' );
	if (isset($_GET['post'])) $post_id = $_GET['post'];
  // The actual fields for data entry
  echo '<label for="stream_url">';
       _e("URL (info only)", 'stream_textdomain' );
  echo '</label> ';
  echo '<input type="text" id="stream_url" name="stream_url" value="'.($post_id ? get_post_meta($post_id,'url',true):'').'" size="80" /><br />';
   echo '<label for="stream_url">';
       _e("Stream URLs (list of urls or URL to .pls playlist file )", 'stream_textdomain' );
  echo '</label> ';
  echo '<textarea id="stream_urls" name="stream_urls" rows="5" cols="80">
'.($post_id ? get_post_meta($post_id,'urls',true):'').'</textarea>';
}

/* When the post is saved, saves our custom data */
function stream_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['stream_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'stream' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // OK, we're authenticated: we need to find and save the data

  $url = $_POST['stream_url'];
  $urls = $_POST['stream_urls'];
 // $urls = explode(',',$_POST['stream_urls']);
 // $urls = serialize($urls);
  add_post_meta($post_id, 'url', $url, true) or update_post_meta($post_id, 'url', $url);
  add_post_meta($post_id, 'urls', $urls, true) or update_post_meta($post_id, 'urls', $urls);

}
function stream_list_func( $atts ) {
	$filter = null;
	$echo = false;
	$list = array();
	if ($atts['id'] != null ) {
		$id = $atts['id'];
	} else {
		$id = -1;
	}
	if ($atts['echo'] == 'true'){
		$echo = true;
	}
	if ($atts['filter'] != null){
		$filer = $atts['filter'];
	}
	$args = array(   
    'post_type'       => 'stream',
    'post_status'     => 'publish' );
	$streams = get_posts($args);
	$counter = 0;
	foreach ($streams as $stream){
		if ($echo){
		if ($id == -1){
			if ($filter == null || $filter == 'title') _e("Stream title: <b>".$stream->post_title."</b></br >");
			if ($filter == null || $filter == 'description') _e("Stream description: <b>".$stream->post_content."</b></br >");
			if ($filter == null || $filter == 'url') echo "Stream URL info: <b>".get_post_meta($stream->ID,'url',true)."</b></br >";
			if ($filter == null || $filter == 'urls') echo "Stream urls: <b>".get_post_meta($stream->ID,'urls',true)."</b></br >";
		} else {
			if ($id == $stream->ID){
				if ($filter == null || $filter == 'title') _e("Stream title: <b>".$stream->post_title."</b></br >");
				if ($filter == null || $filter == 'description') _e("Stream description: <b>".$stream->post_content."</b></br >");
				if ($filter == null || $filter == 'url') echo "Stream URL info: <b>".get_post_meta($stream->ID,'url',true)."</b></br >";
				if ($filter == null || $filter == 'urls') echo "Stream urls: <b>".get_post_meta($stream->ID,'urls',true)."</b></br >";
			}
		}
		} else {
			if ($id == -1){
				$list[$counter][0] = ($stream->post_title);
				$list[$counter][1] = ($stream->post_content);
				$list[$counter][2] = get_post_meta($stream->ID,'url',true);
				$list[$counter][3] = get_post_meta($stream->ID,'urls',true);
				$counter++;
			} else if ($id == $stream->ID){
				$list[$counter][0] = ($stream->post_title);
				$list[$counter][1] = ($stream->post_content);
				$list[$counter][2] = get_post_meta($stream->ID,'url',true);
				$list[$counter][3] = get_post_meta($stream->ID,'urls',true);
				$counter++;
			}
		}		
	}
	if (!$echo ) return $list;
}
add_shortcode( 'stream_list', 'stream_list_func' );
function stream_list_array( $id = -1 ) {
	$args = array(   
    'post_type'       => 'stream',
    'post_status'     => 'publish' );
	$streams = get_posts($args);
	$counter = 0;
	foreach ($streams as $stream){	
			if ($id == -1){
				$list[$counter][0] = ($stream->post_title);
				$list[$counter][1] = ($stream->post_content);
				$list[$counter][2] = get_post_meta($stream->ID,'url',true);
				$list[$counter][3] = get_post_meta($stream->ID,'urls',true);
				$counter++;
			} else if ($id == $stream->ID){
				$list[$counter][0] = ($stream->post_title);
				$list[$counter][1] = ($stream->post_content);
				$list[$counter][2] = get_post_meta($stream->ID,'url',true);
				$list[$counter][3] = get_post_meta($stream->ID,'urls',true);
				$counter++;
			}
				
	}
	return $list;
}
?>
