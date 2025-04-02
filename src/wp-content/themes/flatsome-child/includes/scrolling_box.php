<?php

add_shortcode('scrolling_box', function ($atts, $content = null) {
    $content = '<div class="scrolling-box">' . do_shortcode($content) . '</div>';

    $css = <<<EOF
<style>
.scrolling-box {
    display: block;
    max-height: 840px;
    padding: 14px 7px 14px 0;
    overflow-y: auto;
    text-align: left;
    background:#f3f3f3;
}
</style>
EOF;
    return $css . $content;
});