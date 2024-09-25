# Tạo token
https://github.com/settings/tokens/new
```
git clone https://TOKEN@github.com/jk23yearr/wp-init DOMAIN
```
# Setup Wordpress Site Starter
- Hướng dẫn step by step setup source wp với docker trên localhost. 
- Checkout source code với repo bạn được share, branch [main] với tên Project bạn thực hiện, VD: Project thực hiện có domain là: xxx.com thì tên sẽ là site.xxx.com

- Step 1: Cập nhật Port [PORT] phpmyadmin và wordpress trong docker-compose.yml để sử dụng trên trình duyệt (nên sử dụng các port 4 số như là: 3001, 3002...để tránh conflict với các port thông dụng khác trên PC)

- Step 2: Di chuyển cd vào thư mục mà bạn đã checkout source code và chạy command trên Terminal:
```
docker-compose up -d
```
Source WP sẽ được setup và download về trên máy local của bạn, các services sẽ tự động started nếu không có lỗi xảy ra.

- Step 3: Truy cập link web localhost với port đã cấu hình ở step 1 và thực hiện install site với thông tin DB trong config mặc định của file docker-compose.yml:
```
http://localhost + [PORT]
```
- Step 4: Cập nhật DB db/03-12-2023-21-28.sql
```
http://localhost + [PORT]/index.php?route=/database/import&db=wordpress
```
- Step 5: Cập nhật Git repo
Xóa git repo init (Lưu ý: cd vào đúng thư mục trước khi thực hiện):
```
rm -rf .git
```
Khởi tạo .git mới cho project
```
git init
```
- Step 6: Setup themes default (flatsome) / các project đặc biệt (sử dụng themes khác). Liên hệ LEAD.

- Step 7: Thực hiện project với layout/design.

# Các plugins được dùng ( CÁC PLUGINS KHÁC CHỈ SỬ DỤNG KHI ĐƯỢC LEAD APPROVED)
1. Admin Columns - https://wordpress.org/plugins/codepress-admin-columns/
2. Related Posts Flatsome - https://wordpress.org/plugins/related-posts-flatsome/ (Must review updated)
3. Easy Table of Contents - https://wordpress.org/plugins/easy-table-of-contents/
4. All-in-One WP Migration - https://wordpress.org/plugins/all-in-one-wp-migration/
5. Classic Editor - https://wordpress.org/plugins/classic-editor/
6. Rank Math SEO - https://wordpress.org/plugins/seo-by-rank-math/
7. Instant Indexing - https://rankmath.com/wordpress/plugin/instant-indexing/
8. Solid Security Basic - https://wordpress.org/plugins/better-wp-security/
9. ThumbPress - https://pluggable.io/plugin/thumbpress ( Must review updated )
10. WP File Manager - https://wordpress.org/plugins/wp-file-manager/ (Sử dụng xong phải disabled hoặc removed)
11. LiteSpeed Cache - https://wordpress.org/plugins/litespeed-cache/
12. WP Rocket (Phải có license)
13. Web Icons - https://wordpress.org/plugins/icon/
...and.

# Security
**BẢO MẬT LÀ ƯU TIÊN HÀNG ĐẦU CỦA TEAM DEV VÀ CŨNG LÀ CỦA CTY,** 
**PROJECTS/SẢN PHẨM LÀM RA BỊ HACK ĐÓ LÀ SỰ YẾU KÉM CỦA DEV,** 
**THU NHẬP SẼ BỊ ẢNH HƯỞNG CHỈ VÌ 1 SỰ TẤT TRÁCH NHẤT THỜI**

Try Hard 💪💪💪 and Happy Coding 😉!
_ReadMe sẽ được cập nhật theo issue/sự cố hoặc Policy._
