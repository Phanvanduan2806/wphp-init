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

[ux_html]
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 
<dotlottie-player src="https://lottie.host/fc40f42e-5b4a-41fb-9b12-cc45dd0d2161/LGgJud94N8.json" background="transparent" speed="1" style="width: 100%; height: 300px;" loop autoplay></dotlottie-player>
[/ux_html]
[search style="flat"]

[gap height="20px"]

[button text="Về trang chủ" radius="99" link="/"]


[/col]

[/row]

[/section]
EOF;
echo do_shortcode($result);
get_footer();