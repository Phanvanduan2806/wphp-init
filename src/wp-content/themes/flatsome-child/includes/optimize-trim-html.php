<?php

if (!is_admin() && is_front_page()) {
    add_action('template_redirect', function() {
        ob_start(fn($buffer) => str_replace(array("\r", "\n"), '', $buffer));
        add_action('shutdown', fn() => ob_end_flush(), 1);
    });
}
