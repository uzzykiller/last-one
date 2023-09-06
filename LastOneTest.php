<?php

class LastOneTest extends WP_UnitTestCase {
// global $factory;
	public function test_menu_page_registration() 
	{
		global $menu;
		// Call the registration function
		register_my_custom_menu_page();
		
		// Check if the menu item has been added to the global $menu array
		$found = false;
		foreach ($menu as $item) {
			if ($item[2] == 'custompage') {
				$found = true;
				break;
			}
		}
		// Assert that the menu item has been added
		$this->assertTrue($found, 'The custom menu item is not added.');
	}

    public function sebjtUp() 
	{
        // parent::setUp();

        // Create a user with 'manage_options' capability (administrator)
        $admin_user = $this->factory->user->create(['role' => 'administrator']);
        wp_set_current_user($admin_user);
    }

    public function test_upload_image_and_save_option() 
	{
        // Simulate form submission with an image file
        $_POST['upload_image'] = 'Upload'; // Submit button
        $_FILES['image_upload'] = [
            'name' => 'sample.jpg', // Replace with the desired file name
            'type' => 'image/jpeg',
            'tmp_name' => 'sample.jpg', // Replace with the actual file path
            'error' => UPLOAD_ERR_OK,
            'size' => filesize('sample.jpg'), // Replace with the actual file path
        ];

        // Call the function to process the form
        ob_start();
        my_custom_menu_page();
        ob_end_clean();

        // Check if the image was uploaded and saved to the options
        $saved_image_ids = get_option('plugin_image_id', array());
        $this->assertEmpty($saved_image_ids);
        // Remove the uploaded image (if needed) to clean up the environment
        foreach ($saved_image_ids as $value) {
            wp_delete_attachment($value, true);
        }

        // Clear the $_FILES and $_POST arrays to avoid affecting other tests
        $_FILES = array();
        $_POST = array();
    }

	public function test_render_settings_page() 
	{
        // Call the function to generate the HTML output
        ob_start();
        my_custom_menu_page();
        $output = ob_get_clean();

        // Perform assertions on the rendered HTML output
        $this->assertStringContainsString('<h2>Image Settings</h2>', $output);
        $this->assertStringContainsString('<form method="post" enctype="multipart/form-data">', $output);
        $this->assertStringContainsString('Upload Image:', $output);
        $this->assertStringContainsString('<input type="file" name="image_upload" id="image_upload">', $output);
        $this->assertStringContainsString('<input type="submit" class="button button-primary" name="upload_image" value="Upload">', $output);

        // You can add more assertions to test other parts of the HTML generated by the function.
    }

	public function test_shortcode_output_with_images() 
	{
        // Create a test attachment and add it to the 'plugin_image_id' option
        $attachment_id = $this->factory->attachment->create_upload_object('sample.jpg', 0);

        // Add the attachment ID to the 'plugin_image_id' option
        update_option('plugin_image_id', [$attachment_id]);

        // Execute the shortcode
        $output = do_shortcode('[myslideshow]');

        // Check if the shortcode output contains the expected HTML structure
        $this->assertStringContainsString('<div class="slider-container">', $output);
        $this->assertStringContainsString('<img class="slide"', $output);
        $this->assertStringContainsString('<a class="prev"', $output);
        $this->assertStringContainsString('<a class="next"', $output);

        // Clean up by deleting the test attachment
        wp_delete_attachment($attachment_id, true);
    }

    public function test_shortcode_output_without_images() 
	{
        // Remove any existing values in the 'plugin_image_id' option
        update_option('plugin_image_id', array());

        // Execute the shortcode
        $output = do_shortcode('[myslideshow]');

        // Check if the shortcode output is empty when there are no images
        $this->assertEmpty($output);
    }
}
