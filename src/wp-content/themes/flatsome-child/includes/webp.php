<?php
/*
 * 1/ Thumbnail Deletion
 * 2/ Image Size Management
 * 3/ Filename Sanitization
 * 4/ Image Resizing
 * 5/ Image Conversion to WebP
 * 6/ Convert old image
 * */
class webp_image_convertor_v2
{
    public function __construct()
    {
        add_action('save_post', array($this, 'after_file_saved'), 10, 3);
        add_filter('intermediate_image_sizes_advanced', [$this, 'disable_image_sizes']);
        add_filter('wp_handle_upload_prefilter', [$this, 'rename_file_attachment']);
        add_action('add_attachment', [$this, 'resize_attachment']);
        add_action('add_attachment', [$this, 'convert_attachment_webp'], 20);
        add_action('init', [$this, 'handle_get_request']);
    }

    public function after_file_saved($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if ($post->post_type != 'files') return;
        if ($post->post_status != 'trash') return;
        $attach_id = get_post_thumbnail_id($post_id);
        wp_delete_attachment($attach_id, true);
    }

    public function disable_image_sizes($sizes)
    {
        unset($sizes['thumbnail']);
        unset($sizes['medium']);
        unset($sizes['medium_large']);
        unset($sizes['large']);
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        return $sizes;
    }

    public function rename_file_attachment($file)
    {
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name_without_extension = pathinfo($file['name'], PATHINFO_FILENAME);
        $file_name_without_extension = sanitize_title($file_name_without_extension);
        $file['name'] = $file_name_without_extension . '.' . $file_extension;
        return $file;
    }

    public function resize_attachment($attachment_id)
    {
        [$info, $file_path] = $this->get_info($attachment_id);
        if (!$file_path) return;
        if (@$info[2] == IMAGETYPE_GIF) return;
        $image_editor = wp_get_image_editor($file_path);
        if (is_wp_error($image_editor)) return;
        if ($image_editor instanceof WP_Image_Editor === false) return;
        $image_editor->resize(1400, null, false);
        $saved = $image_editor->save($file_path);
        $image_meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);
        if (is_string($image_meta)) {
            $image_meta = json_decode($image_meta, true);
        }
        if (is_array($image_meta)) {
            $image_meta['height'] = $saved['height'];
            $image_meta['width'] = $saved['width'];
            update_post_meta($attachment_id, '_wp_attachment_metadata', $image_meta);
        }
    }

    private function get_info($attachment_id)
    {
        $file_path = get_attached_file($attachment_id);
        if (!file_exists($file_path)) return false;
        return [getimagesize($file_path), $file_path];
    }

    public function convert_attachment_webp($attachment_id)
    {
        [$info, $file_path] = $this->get_info($attachment_id);
        if (!$file_path) return;
        if (@$info[2] == IMAGETYPE_GIF) return;
        if ($info !== false) {
            $image = null;
            switch ($info[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file_path);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file_path);
                    if (!imageistruecolor($image)) {
                        // Create a new true color image and copy the original image to it
                        $width = imagesx($image);
                        $height = imagesy($image);
                        $trueColorImage = imagecreatetruecolor($width, $height);
                        imagealphablending($trueColorImage, false);
                        imagesavealpha($trueColorImage, true);
                        imagecopyresampled($trueColorImage, $image, 0, 0, 0, 0, $width, $height, $width, $height);
                        imagedestroy($image); // Destroy the original palette image
                        $image = $trueColorImage; // Use the new true color image for WebP conversion
                    }
                    break;
            }
            if ($image != null) {
                $file_path_without_extension = preg_replace('/\.[^.]+$/', '', $file_path);
                $webp_path = $file_path_without_extension . '.webp';
                if (!imagewebp($image, $webp_path, 100)) {
                    error_log("Failed to convert image to WebP: $file_path");
                } else {
                    $attachment = get_post($attachment_id);
                    update_post_meta($attachment_id, '_wp_attached_file', $webp_path);
                }
                imagedestroy($image);
            }
        }
    }


    public function handle_get_request()
    {
        if (isset($_GET['image'])) {
            $page_number = intval($_GET['image']);
            $this->convert_all_images_to_webp($page_number);
        }
    }

    private function convert_all_images_to_webp($page_number = 1)
    {
        $conversion_results = array();
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => array('image/jpeg', 'image/png'),
            'post_status' => 'inherit',
            'posts_per_page' => 500,
            'paged' => $page_number,
        );
        $attachments = new WP_Query($args);
        $index = $args['posts_per_page'] * ($page_number - 1);

        if ($attachments->have_posts()) : while ($attachments->have_posts()) : $attachments->the_post();
            $index++;
            error_log('Doing: ' . $index);
            $attachment_id = get_the_ID();
            $old_file_url = wp_get_attachment_url($attachment_id);
            $this->convert_attachment_webp($attachment_id);
            $new_file_url = wp_get_attachment_url($attachment_id);
            $conversion_results[] = [$old_file_url, $new_file_url];

            $posts_args = array(
                'post_type' => 'any',
                'posts_per_page' => -1,
                's' => $old_file_url,
            );
            $posts_query = new WP_Query($posts_args);

            if ($posts_query->have_posts()) : while ($posts_query->have_posts()) : $posts_query->the_post();
                $post_id = get_the_ID();
                $post_content = get_post_field('post_content', $post_id);
                $updated_content = str_replace($old_file_url, $new_file_url, $post_content);
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_content' => $updated_content,
                ));
            endwhile; endif;
            wp_reset_postdata();

        endwhile; endif;
        wp_reset_postdata();

        echo '<pre>' . htmlspecialchars(print_r($conversion_results, true)) . '</pre>';
        wp_die();
    }
}

new webp_image_convertor_v2();