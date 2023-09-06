<?php

/*
 * Plugin Name:       LAST-ONE
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Buddy
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       last-one-plugin
 * Domain Path:       /languages
 */

include plugin_dir_path( __FILE__ ) . '/script.php';

/**
 * Register a custom menu page.
 */
function register_my_custom_menu_page()
{
    add_menu_page(
        __('Custom Menu Title', 'last-one-plugin'),
        'Custom Menu',
        'manage_options',
        'custompage',
        'my_custom_menu_page',
        'dashicons-schedule',
        3
    );
}
add_action( 'admin_menu', 'register_my_custom_menu_page' );

// Render the settings page content
function my_custom_menu_page()
{
    ?>
    <div class="wrap">
        <h2>Image Settings</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="image_upload">Upload Image:</label>
            <input type="file" name="image_upload" id="image_upload">
            <input type="submit" class="button button-primary" name="upload_image" value="Upload">
        </form>
        <?php
        if (isset($_POST['upload_image'])) {
            if (!empty($_FILES['image_upload']['name'])) {
                $uploaded_image_id = media_handle_upload('image_upload', 0);
                if (is_wp_error($uploaded_image_id)) {
                    // Handle upload error
                    echo 'Image upload failed: ' . $uploaded_image_id->get_error_message();
                } else {
                    $saved_image_ids = get_option('plugin_image_id', array());
                    $saved_image_ids[] = $uploaded_image_id;
                    update_option('plugin_image_id', $saved_image_ids);
                }
            }
        }

        // Display existing images and remove option
        $saved_image_ids = get_option('plugin_image_id', array());
        if (!empty($saved_image_ids)) {
            ?>
                <div id="imageListId">
                    <?php
                    foreach ($saved_image_ids as $value) {
                            // Use wp_get_attachment_image_src to get the image URL
                            $image_url = wp_get_attachment_image_src($value, 'full');
                            if ($image_url) {
                            ?>
                                <div class="image">
                                    <?php
                                        echo '<img class="listitemClass" id="imageNo" src="' . esc_url($image_url[0]) . '" alt="Uploaded Image" ><br>';
                                        echo '<a class="button button-secondry" href="?remove_image=' . $value . '" >Remove Image</a>';
                                    ?>
                                </div>
                            <?php
                            }
                        }
                    ?>
                </div>
            <?php
        }
        ?>
    </div>
    <?php
}
    // Handle image removal
    if (isset($_GET['remove_image'])) 
    {
        $image_id = sanitize_text_field($_GET['remove_image']);
        $saved_image_ids = get_option('plugin_image_id', array());
        if (in_array($image_id, $saved_image_ids)) {
            $index = array_search($image_id, $saved_image_ids);
            unset($saved_image_ids[$index]);
            update_option('plugin_image_id', $saved_image_ids);
            wp_delete_attachment($image_id, true); // Delete the image from the media library
        }
    }

function display_uploaded_images_slider() 
{
    $values = get_option('plugin_image_id', array());
    if (!empty($values)) {
        $output = '<div class="slider-container">
            <div class="slider-images">';
        foreach ($values as $value) {
            $image_url = wp_get_attachment_image_src($value, 'full');   
            if ($image_url) {
                $output .= '<img class="slide" src="' . esc_url($image_url[0]) . '" alt="Uploaded Image" />';  
            }
        }
        $output .= '</div>
        </div>
        <div class="">
            <a class="prev" onclick="prevSlide()">&#10094;</a>
            <a class="next" onclick="nextSlide()">&#10095;</a>
        </div>';
        return $output;
    }
    return ''; // Return empty string if there are no images
}
add_shortcode('myslideshow', 'display_uploaded_images_slider');


