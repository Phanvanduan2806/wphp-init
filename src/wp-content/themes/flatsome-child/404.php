<?php
/**
 * The blog template file.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();

$result = <<<EOF
[section]

[row h_align="center"]

[col span="6" span__sm="12" align="center"]

[title style="center" text="Trang không tìm thấy" tag_name="h1" class="a-heading"]

[search style="flat"]

[gap height="20px"]

[button text="Về trang chủ" radius="99" link="/"]


[/col]

[/row]

[/section]
EOF;
echo do_shortcode($result);
get_footer();