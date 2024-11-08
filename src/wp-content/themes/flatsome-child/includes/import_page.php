<?php
/*
Plugin Name: Custom Import Page
Description: Adds a custom import page to clear old data and handle result output with deletion and addition logs.
Version: 1.1
Author: Your Name
*/

// Hook to add the custom admin page
function custom_import_page() {
    add_menu_page(
        'Custom Import',
        'Custom Import',
        'manage_options',
        'custom-import',
        'render_custom_import_page',
        'dashicons-upload',
        6
    );
}
add_action('admin_menu', 'custom_import_page');

// Function to render the admin page
function render_custom_import_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result_output = sanitize_textarea_field($_POST['result_output']);
        $clear_posts = isset($_POST['clear_posts']);
        $clear_pages = isset($_POST['clear_pages']);
        $clear_categories = isset($_POST['clear_categories']);
        $clear_media = isset($_POST['clear_media']);

        echo '<div class="updated"><p>';

        if ($clear_posts) {
            $posts = get_posts(['post_type' => 'post', 'numberposts' => -1]);
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
                echo "Deleted post: {$post->post_title}<br>";
            }
        }

        if ($clear_pages) {
            $homepage_id = get_option('page_on_front');
            $pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);
            foreach ($pages as $page) {
                if ($page->ID != $homepage_id) {
                    wp_delete_post($page->ID, true);
                    echo "Deleted page: {$page->post_title}<br>";
                }
            }
        }

        if ($clear_categories) {
            $categories = get_categories(['hide_empty' => false]);
            foreach ($categories as $category) {
                wp_delete_term($category->term_id, 'category');
                echo "Deleted category: {$category->name}<br>";
            }
        }

        if ($clear_media) {
            $media = get_posts(['post_type' => 'attachment', 'numberposts' => -1]);
            foreach ($media as $file) {
                wp_delete_attachment($file->ID, true);
                echo "Deleted media: {$file->post_title}<br>";
            }
        }

        // Parse result_output and import based on type
        $lines = explode("\n", $result_output);
        echo "<hr/>";
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            list($type, $value) = explode(",", $line, 2);
            $type = trim(strtolower($type));
            $value = trim($value);

            switch ($type) {
                case 'image':
                    import_image($value);
                    echo "Added image from URL: $value<br>";
                    break;
                case 'category':
                    wp_insert_term($value, 'category');
                    echo "Added category: $value<br>";
                    break;
                case 'page':
                    wp_insert_post([
                        'post_title' => $value,
                        'post_type' => 'page',
                        'post_status' => 'publish'
                    ]);
                    echo "Added page: $value<br>";
                    break;
                case 'post':
                    wp_insert_post([
                        'post_title' => $value,
                        'post_type' => 'post',
                        'post_status' => 'publish'
                    ]);
                    echo "Added post: $value<br>";
                    break;
            }
        }

        echo 'Data imported successfully!</p></div>';
    }

    // Render the form
    ?>
    <div class="wrap">
        <h1>Custom Import</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="result_output">Result Output</label>
                    </th>
                    <td>
                        <textarea name="result_output" rows="5" cols="50" required></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Options</th>
                    <td>
                        <label>
                            <input type="checkbox" name="clear_posts" checked>
                            Clear All Old Posts
                        </label><br>
                        <label>
                            <input type="checkbox" name="clear_pages" checked>
                            Clear All Old Pages
                        </label><br>
                        <label>
                            <input type="checkbox" name="clear_categories" checked>
                            Clear All Old Categories
                        </label><br>
                        <label>
                            <input type="checkbox" name="clear_media" checked>
                            Clear All Old Media
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Run Import'); ?>
        </form>
    </div>
    <?php
}

// Function to import image by URL
function import_image($url) {
    $tmp = download_url($url);

    if (is_wp_error($tmp)) {
        echo "Failed to download image from URL: $url<br>";
        return;
    }

    $file_array = array(
        'name' => basename($url),
        'tmp_name' => $tmp
    );

    $id = media_handle_sideload($file_array, 0);

    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        echo "Failed to import image from URL: $url<br>";
    }
}
?>
