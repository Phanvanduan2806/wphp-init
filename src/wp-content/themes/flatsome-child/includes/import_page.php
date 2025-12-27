<?php
/*
Plugin Name: Custom Import Page
Description: Adds a custom import page to clear old data and handle result output with deletion and addition logs.
Version: 1.2
*/

function custom_import_page() {
    add_menu_page('Custom Import', 'Custom Import', 'manage_options', 'custom-import', 'render_custom_import_page', 'dashicons-upload', 6);
}
add_action('admin_menu', 'custom_import_page');

function render_custom_import_page() {
    if (!current_user_can('manage_options')) return;

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['custom_import_nonce']) && wp_verify_nonce($_POST['custom_import_nonce'], 'custom_import_action')) {
        
        $result_output = sanitize_textarea_field($_POST['result_output']);
        $import_mode   = $_POST['import_mode'];
        
        echo '<div class="updated" style="max-height: 400px; overflow-y: auto; padding: 15px; border-left: 4px solid #00a0d2; background: #fff;"><p>';

        $delete_keys = ['clear_posts' => 'post', 'clear_pages' => 'page', 'clear_categories' => 'category', 'clear_media' => 'attachment'];
        foreach ($delete_keys as $post_key => $type) {
            if (isset($_POST[$post_key])) {
                if ($type === 'category') {
                    foreach (get_categories(['hide_empty' => false]) as $cat) {
                        if ($cat->slug !== 'uncategorized') wp_delete_term($cat->term_id, 'category');
                    }
                } else {
                    $items = get_posts(['post_type' => $type, 'numberposts' => -1, 'post_status' => 'any']);
                    foreach ($items as $item) {
                        if ($type === 'page' && $item->ID == get_option('page_on_front')) continue;
                        if ($type === 'attachment') { wp_delete_attachment($item->ID, true); }
                        else { wp_delete_post($item->ID, true); }
                    }
                }
                echo "<span style='color: #d63638;'>✕ Đã xóa dữ liệu cũ loại: <strong>$type</strong></span>.<br>";
            }
        }

        $lines = explode("\n", $result_output);
        echo "<hr style='border: 0; border-top: 1px solid #eee; margin: 10px 0;'/>";
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode(",", $line, 2);
            $type = trim(strtolower($parts[0]));
            $value = isset($parts[1]) ? trim($parts[1]) : '';

            switch ($type) {
                case 'image':
                    $img_id = import_image_from_url($value);
                    if ($img_id) echo "<span style='color: #2271b1;'>📷 Media:</span> $value (ID: $img_id)<br>";
                    break;

                case 'category':
                    $cat_slug = sanitize_title($value);
                    if (!term_exists($cat_slug, 'category')) {
                        wp_insert_term($value, 'category', ['slug' => $cat_slug]);
                        echo "<span style='color: #2271b1;'>📁 Category:</span> $value<br>";
                    }
                    break;

                case 'page':
                    $page_data = explode(",", $value);
                    $page_title = trim($page_data[0]);
                    $page_img   = isset($page_data[1]) ? trim($page_data[1]) : '';

                    if (get_page_by_title($page_title, OBJECT, 'page')) {
                        echo "<span style='color: #999;'>⏭ Bỏ qua Page: $page_title (Đã có)</span><br>";
                    } else {
                        $page_id = wp_insert_post(['post_title' => $page_title, 'post_type' => 'page', 'post_status' => 'publish']);
                        if ($page_id && !empty($page_img)) {
                            $img_id = import_image_from_url($page_img);
                            if ($img_id) set_post_thumbnail($page_id, $img_id);
                        }
                        echo "<span style='color: #673ab7;'>📄 Page:</span> $page_title<br>";
                    }
                    break;

                case 'post':
                    $post_data = explode(",", $value);
                    if ($import_mode === 'with_cate') {
                        $c_name = isset($post_data[0]) ? trim($post_data[0]) : '';
                        $p_title = isset($post_data[1]) ? trim($post_data[1]) : '';
                        $p_img   = isset($post_data[2]) ? trim($post_data[2]) : '';
                    } else {
                        $c_name = '';
                        $p_title = isset($post_data[0]) ? trim($post_data[0]) : '';
                        $p_img   = isset($post_data[1]) ? trim($post_data[1]) : '';
                    }

                    if (empty($p_title)) continue 2;
                    if (get_page_by_title($p_title, OBJECT, 'post')) {
                        echo "<span style='color: #999;'>⏭ Bỏ qua Post: $p_title (Đã có)</span><br>"; continue 2;
                    }

                    $cat_ids = [];
                    if (!empty($c_name)) {
                        $c_slug = sanitize_title($c_name);
                        $term = get_term_by('slug', $c_slug, 'category');
                        if ($term) { $cat_ids = [(int)$term->term_id]; }
                        else {
                            $new_cat = wp_insert_term($c_name, 'category', ['slug' => $c_slug]);
                            if (!is_wp_error($new_cat)) $cat_ids = [(int)$new_cat['term_id']];
                        }
                    }

                    $post_id = wp_insert_post(['post_title' => $p_title, 'post_type' => 'post', 'post_status' => 'publish', 'post_category' => $cat_ids]);
                    if ($post_id && !empty($p_img)) {
                        $img_id = import_image_from_url($p_img);
                        if ($img_id) set_post_thumbnail($post_id, $img_id);
                    }
                    echo "<span style='color: #4caf50;'>📝 Post:</span> $p_title<br>";
                    break;
            }
        }
        echo '<br><strong>✅ Tất cả đã được xử lý!</strong></p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Custom Import Professional v1.8.1</h1>
        
        <script>
            function checkConfirm() {
                const deletes = document.querySelectorAll('input[name^="clear_"]:checked');
                if (deletes.length > 0) return confirm('❗ CẢNH BÁO: Bạn đã chọn XÓA dữ liệu. Dữ liệu sẽ mất vĩnh viễn. Bạn có chắc chắn?');
                return true;
            }
        </script>

        <form method="post" onsubmit="return checkConfirm();">
            <?php wp_nonce_field('custom_import_action', 'custom_import_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Dữ liệu đầu vào</th>
                    <td>
                        <textarea name="result_output" rows="10" style="width:100%; font-family:Consolas, Monaco, monospace;" placeholder="Loại data, Giá trị 1, Giá trị 2..."></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Chế độ Import Bài viết</th>
                    <td>
                        <label><input type="radio" name="import_mode" value="with_cate" checked> <strong>Có gán Category</strong> (Cấu trúc: <code>post, Cate, Title, Img</code>)</label><br>
                        <label><input type="radio" name="import_mode" value="no_cate"> <strong>Không gán Category</strong> (Cấu trúc: <code>post, Title, Img</code>)</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lựa chọn xóa dữ liệu</th>
                    <td style="background:#fff5f5; border:1px solid #ffcccc; padding:15px; border-radius: 4px;">
                        <label><input type="checkbox" name="clear_posts"> Posts</label> | 
                        <label><input type="checkbox" name="clear_pages"> Pages</label> | 
                        <label><input type="checkbox" name="clear_categories"> Categories</label> | 
                        <label><input type="checkbox" name="clear_media"> Media Library</label>
                        <p class="description" style="color:red; margin-top: 5px;">*Các mục được chọn sẽ bị xóa sạch trước khi thêm mới.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Bắt đầu Import ngay'); ?>
        </form>

        <hr>
        <h2>Cấu trúc nhập:</h2>
        <table class="widefat striped" style="max-width: 900px;">
            <thead><tr><th>Loại</th><th>Cấu trúc</th></tr></thead>
            <tbody>
                <tr><td><strong>Post (Tự động gán category)</strong></td><td><code>post, Thể loại, Tiêu đề bài viết, Link ảnh</code></td></tr>
                <tr><td><strong>Post (uncategory)</strong></td><td><code>post, Tiêu đề bài viết, Link ảnh</code></td></tr>
                <tr><td><strong>Page</strong></td><td><code>page, Tiêu đề trang, Link ảnh</code></td></tr>
                <tr><td><strong>Category</strong></td><td><code>category, Tên danh mục mới</code></td></tr>
                <tr><td><strong>Image</strong></td><td><code>image, Link ảnh</code></td></tr>
            </tbody>
        </table>
        <p><i>* Gợi ý: Link ảnh có thể bỏ trống nếu bạn chỉ muốn tạo nội dung.</i></p>
    </div>
    <?php
}

function import_image_from_url($url) {
    if (empty($url)) return null;
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $tmp = download_url($url);
    if (is_wp_error($tmp)) return null;
    $file = ['name' => basename($url), 'tmp_name' => $tmp];
    $id = media_handle_sideload($file, 0);
    if (is_wp_error($id)) { @unlink($tmp); return null; }
    return $id;
}
