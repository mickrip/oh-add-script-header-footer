<?php
/**
 * Plugin Name: OH Add Script Header Footer
 * Plugin URI: http://ohav.co.il/plugin
 * Description:  create a simple way to add js code to individual page post or custom post type header and footer, in this way it enable you to add google re-marketing code to individual pages
 * Version:1.2.1
 * Author: orenhav, rel78
 * Author URI: http://ohav.co.il
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */


// add google analytics to footer
function oh_add_script() {
	global $post;
	$output = get_post_meta($post->ID,'_oh_add_script_header',true);
	echo stripslashes($output);
	 
}
function oh_add_script_footer() {
	global $post;
	$output = get_post_meta($post->ID,'_oh_add_script_footer',true);
	echo stripslashes($output);
	 
}
add_action('wp_head', 'oh_add_script');
add_action('wp_footer', 'oh_add_script_footer');
 
 
 

/* Define the custom box */

add_action( 'add_meta_boxes', 'oh_script_add_custom_box' );


/* Do something with the data entered */
add_action( 'save_post', 'oh_script_save_custom_box' );

/* Adds a box to the main column on the Post and Page edit screens */
function oh_script_add_custom_box() {

	$screens = get_post_types('','names');
    $screens = array_merge($screens,array( 'post', 'page' ));
	
    foreach ($screens as $screen) {
        add_meta_box(
            'myplugin_sectionid',
            __( 'OH add script', 'oh_add_script' ),
            'oh_script_inner_custom_box',
            $screen
        );
    }
	
	 
	
}

/* Prints the box content */
function oh_script_inner_custom_box( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'oh_add_script_noncename' );

  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $value = get_post_meta( $post->ID, '_oh_add_script_header',  true );
  $value_footer = get_post_meta( $post->ID, '_oh_add_script_footer',  true );
  echo '<label for="oh_add_script_header">';
       _e("add script to be added to the header of the page", 'oh_add_script' );
  echo '</label> ';
  echo '<textarea  style="display: block;width: 90%;min-height: 150px;" id="oh_add_script_header"
            name="oh_add_script_header"   size="25" >'.$value.'</textarea>';
  echo '<label for="oh_add_script_footer">';
       _e("add script to be added to the footer of the page", 'oh_add_script_footer' );
  echo '</label> ';
  echo '<textarea  style="display: block;width: 90%;min-height: 150px;" id="oh_add_script_footer"
                name="oh_add_script_footer"   size="25" >'.$value_footer.'</textarea>';
    echo '<p>';
    _e("You should put the code with the script tags<code> &lt;script type='text/javascript'&gt; the code &lt;/script&gt;</code>", 'oh_add_script_footer' );
    echo '</p> ';
  
  
  }

/* When the post is saved, saves our custom data */
function oh_script_save_custom_box( $post_id ) {

  // First we need to check if the current user is authorised to do this action. 
  if ( isset($_POST['post_type']) &&  'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['oh_add_script_noncename'] ) || ! wp_verify_nonce( $_POST['oh_add_script_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  // Thirdly we can save the value to the database

  //if saving in a custom table, get post_ID
  $post_ID = isset($_POST['post_ID']) ? $_POST['post_ID'] : -1;
  //sanitize user input
  $header_script = isset($_POST['oh_add_script_header']) ? $_POST['oh_add_script_header'] : '';

  $footer_script = isset($_POST['oh_add_script_footer']) ? $_POST['oh_add_script_footer'] : '';

  update_post_meta($post_ID, '_oh_add_script_header', $header_script);
  update_post_meta($post_ID, '_oh_add_script_footer', $footer_script);

}
