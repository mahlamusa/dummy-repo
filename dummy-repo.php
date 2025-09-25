<?php
/**
 * Plugin Name: 😂 Giggle Generator
 * Description: Adds humor to WordPress admin!
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Author: Comedy Inc <comedy@example.com>
 * Text Domain: dummy-repo
 * License: GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @category Plugin
 * @package  GiggleGenerator
 * @author   Comedy Inc <comedy@example.com>
 * @license  GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.txt
 * @link     https://comedy.example.com
 */

defined('ABSPATH') || exit;

/**
 * Add funny admin notices.
 *
 * @return void
 */
function Giggle_notice()
{
    if (1 === wp_rand(1, 4)) {
        $jokes = array(
            '☕ Your code is so good, even bugs are impressed!',
            '🐱 A cat fixed your bug by walking on keyboard.',
            '🚀 Your WordPress is running faster than expected.',
            '🎪 Welcome to the circus! You\'re the ringmaster.',
        );
        $joke = $jokes[array_rand($jokes)];
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>Giggle:</strong> ' . esc_html($joke) . '</p>';
        echo '</div>';
    }
}

/**
 * Replace footer text.
 *
 * @return string
 */
function Giggle_footer()
{
    $footers = array(
        'Powered by coffee ☕ and questionable decisions.',
        'Built with ❤️ and too much Stack Overflow.',
        'Tested by highly trained monkeys. 🐵',
    );
    return $footers[array_rand($footers)];
}

if (is_admin()) {
    add_action('admin_notices', 'Giggle_notice');
    add_filter('admin_footer_text', 'Giggle_footer');
}
