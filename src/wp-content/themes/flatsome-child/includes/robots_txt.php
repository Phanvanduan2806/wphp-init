<?php
add_filter('robots_txt', function () {
    $output = '';
    if (get_option('blog_public') == 0) {
        $output .= "User-agent: *\n";
        $output .= "Disallow: /";
    } else {
        $output .= "# All bots\n";
        $output .= "User-agent: *\n";
        $output .= "Disallow: /*?*\n";
        $output .= "Disallow: /wp-admin/\n";
        $output .= "Disallow: /wp-login.php*\n";
        $output .= "Disallow: /customize.php*\n";
        $output .= "Disallow: /?S=\n";
        $output .= "Disallow: /p=*\n";
        $output .= "Disallow: /?p=*\n";
        $output .= "Disallow: /wp-ison/\n";
        $output .= "Disallow: /xmlrpc.php*\n";
        $output .= "Disallow: /comment.php*\n";
        $output .= "Disallow: /wp-includes/\n";
        $output .= "Disallow: /plugins/\n";
        $output .= "Disallow: /themes/\n";
        $output .= "Allow: /uploads/\n";
        $output .= "Allow: /*?a=*\n";
        $output .= "Allow: /*?brandcode=*\n";
        $output .= "Allow: /\n\n";

        // Googlebot-specific rules
        $output .= "# Googlebot\n";
        $output .= "User-agent: Googlebot\n";
        $output .= "Disallow: /feed/\n";
        $output .= "Disallow: /feed$\n";
        $output .= "Disallow: /comments/\n";
        $output .= "Disallow: /?add-to-cart=\n";
        $output .= "Disallow: /?add_to_wishlist=\n\n";

        // Google News bot rules
        $output .= "# Google News\n";
        $output .= "User-agent: Googlebot-News\n";
        $output .= "Allow: /feed\n";
        $output .= "Allow: /feed/\n\n";

        // Block GPTBot
        $output .= "# Block GPTBot\n";
        $output .= "User-agent: GPTBot\n";
        $output .= "Disallow: /\n\n";

        // Block feed for all
        $output .= "# Block feed\n";
        $output .= "User-agent: *\n";
        $output .= "Disallow: */feed/\n\n";

        // Block AI crawling
        $output .= "# Block AI\n";
        $output .= "User-agent: Google-Extended\n";
        $output .= "Disallow: /\n\n";

        // Sitemap
        $output .= "# Sitemap\n";
        $output .= "Sitemap: " . home_url('/sitemap_index.xml');
    }
    return $output;
}, 10, 2);

