<?php
/**
 * Plugin Name: 😂 Giggle Generator Pro
 * Description: Advanced humor system with mood tracking, daily motivation, and interactive features!
 * Version: 2.0.0
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
 * Main plugin class
 */
class GiggleGenerator
{

    /**
     * Initialize the plugin
     */
    public static function init()
    {
        add_action('admin_notices', [__CLASS__, 'show_random_notice']);
        add_filter('admin_footer_text', [__CLASS__, 'funny_footer']);
        add_action('wp_dashboard_setup', [__CLASS__, 'add_dashboard_widget']);
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('wp_ajax_mood_boost', [__CLASS__, 'ajax_mood_boost']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        add_action('admin_init', [__CLASS__, 'track_page_visits']);

        // Create database table on activation
        register_activation_hook(__FILE__, [__CLASS__, 'create_stats_table']);
    }

    /**
     * Create statistics table
     */
    public static function create_stats_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giggle_stats';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            stat_type varchar(50) NOT NULL,
            stat_value int(11) DEFAULT 0,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY stat_type (stat_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Initialize stats
        $wpdb->replace($table_name, ['stat_type' => 'giggles_delivered', 'stat_value' => 0]);
        $wpdb->replace($table_name, ['stat_type' => 'mood_boosts', 'stat_value' => 0]);
        $wpdb->replace($table_name, ['stat_type' => 'admin_visits', 'stat_value' => 0]);
    }

    /**
     * Track admin page visits
     */
    public static function track_page_visits()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giggle_stats';
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET stat_value = stat_value + 1 WHERE stat_type = %s",
                'admin_visits'
            )
        );
    }

    /**
     * Get statistics from database
     */
    public static function get_stats()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giggle_stats';
        $results = $wpdb->get_results("SELECT stat_type, stat_value FROM $table_name");

        $stats = [];
        foreach ($results as $row) {
            $stats[$row->stat_type] = $row->stat_value;
        }
        return $stats;
    }

    /**
     * Update statistics
     */
    public static function update_stat($stat_type)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'giggle_stats';
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET stat_value = stat_value + 1 WHERE stat_type = %s",
                $stat_type
            )
        );
    }

    /**
     * Show random admin notices with tracking
     */
    public static function show_random_notice()
    {
        if (1 === wp_rand(1, 4)) {
            $jokes = [
                '☕ Your code is so good, even bugs are impressed!',
                '🐱 A cat fixed your bug by walking on keyboard.',
                '🚀 Your WordPress is running faster than expected.',
                '🎪 Welcome to the circus! You\'re the ringmaster.',
                '🧙‍♂️ Your database queries are so optimized, they bend spacetime.',
                '🎯 You hit ctrl+s so much, even autosave is jealous.',
                '🍕 Your code is like pizza - even when bad, still pretty good.',
                '🤖 Your functions are so efficient, even robots ask for tips.',
                '🐛 99 bugs in the code, fix one bug, 127 bugs in the code!',
                '🔥 Your CSS is so clean, Marie Kondo wants to learn from you.',
            ];

            $joke = $jokes[array_rand($jokes)];
            self::update_stat('giggles_delivered');

            echo '<div class="notice notice-info is-dismissible giggle-notice" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">';
            echo '<p><strong>🎭 Giggle Generator:</strong> ' . esc_html($joke) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Funny footer text
     */
    public static function funny_footer()
    {
        $footers = [
            'Powered by coffee ☕ and questionable decisions.',
            'Built with ❤️ and too much Stack Overflow.',
            'Tested by highly trained monkeys. 🐵',
            'Currently running on ' . wp_rand(1, 99) . '% magic and ' . wp_rand(1, 99) . '% caffeine.',
            'No developers were harmed in the making of this website.',
        ];
        return $footers[array_rand($footers)];
    }

    /**
     * Add dashboard widget
     */
    public static function add_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'giggle_motivation_widget',
            '🎯 Daily Coding Motivation',
            [__CLASS__, 'dashboard_widget_content']
        );
    }

    /**
     * Dashboard widget content
     */
    public static function dashboard_widget_content()
    {
        $stats = self::get_stats();
        $motivations = [
            'Today is a great day to write some amazing code! 💻',
            'Remember: Every expert was once a beginner. Keep coding! 🌟',
            'Your next commit could be the one that changes everything! 🚀',
            'Debugging is like being a detective in a crime novel! 🔍',
            'Coffee + Code = Magic ✨ (Scientifically proven*)',
            'The best error messages are the ones you never see! 🎯',
        ];

        $motivation = $motivations[array_rand($motivations)];
        $progress = min(100, ($stats['admin_visits'] ?? 0) * 2);

        echo '<div class="giggle-widget-content">';
        echo '<div class="motivation-quote">' . esc_html($motivation) . '</div>';
        echo '<div class="stats-container">';
        echo '<div class="stat-item">🎭 Giggles Delivered: <strong>' . ($stats['giggles_delivered'] ?? 0) . '</strong></div>';
        echo '<div class="stat-item">⚡ Mood Boosts: <strong>' . ($stats['mood_boosts'] ?? 0) . '</strong></div>';
        echo '<div class="stat-item">👥 Admin Visits: <strong>' . ($stats['admin_visits'] ?? 0) . '</strong></div>';
        echo '</div>';
        echo '<div class="productivity-meter">';
        echo '<div class="meter-label">Productivity Level:</div>';
        echo '<div class="meter-bar"><div class="meter-fill" style="width: ' . $progress . '%"></div></div>';
        echo '<div class="meter-text">' . $progress . '%</div>';
        echo '</div>';
        echo '<button id="mood-boost-btn" class="button button-primary mood-boost-btn">🚀 Need a Mood Boost?</button>';
        echo '<div id="mood-boost-result" class="mood-boost-result"></div>';
        echo '</div>';
    }

    /**
     * Add admin menu
     */
    public static function add_admin_menu()
    {
        add_options_page(
            'Giggle Generator Settings',
            '😂 Giggle Generator',
            'manage_options',
            'giggle-generator',
            [__CLASS__, 'admin_page_content']
        );
    }

    /**
     * Admin page content
     */
    public static function admin_page_content()
    {
        $stats = self::get_stats();
        echo '<div class="wrap giggle-admin-page">';
        echo '<h1>🎭 Giggle Generator Pro Dashboard</h1>';

        echo '<div class="giggle-cards">';

        // Stats card
        echo '<div class="giggle-card stats-card">';
        echo '<h2>📊 Your Laughter Statistics</h2>';
        echo '<div class="big-stats">';
        echo '<div class="big-stat"><span class="number">' . ($stats['giggles_delivered'] ?? 0) . '</span><br>Giggles Delivered</div>';
        echo '<div class="big-stat"><span class="number">' . ($stats['mood_boosts'] ?? 0) . '</span><br>Mood Boosts</div>';
        echo '<div class="big-stat"><span class="number">' . ($stats['admin_visits'] ?? 0) . '</span><br>Admin Visits</div>';
        echo '</div>';
        echo '</div>';

        // Joke testing card
        echo '<div class="giggle-card joke-tester">';
        echo '<h2>🧪 Joke Laboratory</h2>';
        echo '<p>Test our jokes and rate them!</p>';
        echo '<button id="test-joke-btn" class="button button-secondary">🎲 Generate Random Joke</button>';
        echo '<div id="joke-display" class="joke-display"></div>';
        echo '</div>';

        // Achievement card
        echo '<div class="giggle-card achievements">';
        echo '<h2>🏆 Achievements Unlocked</h2>';
        echo '<div class="achievement-list">';

        if (($stats['giggles_delivered'] ?? 0) >= 10) {
            echo '<div class="achievement unlocked">🎭 Comedian - Delivered 10+ giggles</div>';
        }
        if (($stats['mood_boosts'] ?? 0) >= 5) {
            echo '<div class="achievement unlocked">⚡ Motivator - Boosted mood 5+ times</div>';
        }
        if (($stats['admin_visits'] ?? 0) >= 50) {
            echo '<div class="achievement unlocked">🏠 Homebody - 50+ admin visits</div>';
        }

        echo '<div class="achievement locked">🌟 Legend - Coming soon...</div>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    /**
     * Handle AJAX mood boost requests
     */
    public static function ajax_mood_boost()
    {
        check_ajax_referer('giggle_nonce', 'nonce');

        $boosts = [
            '🌟 You are absolutely crushing it today!',
            '💪 Your code has the power to change the world!',
            '🚀 Every bug you fix makes the internet a better place!',
            '⚡ Your debugging skills are legendary!',
            '🎯 You turn coffee into code like a wizard!',
            '🌈 Your creativity knows no bounds!',
            '🔥 You are the Neo of the Matrix called programming!',
        ];

        $boost = $boosts[array_rand($boosts)];
        self::update_stat('mood_boosts');

        wp_send_json_success(['message' => $boost]);
    }

    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts($hook)
    {
        // Only load on admin pages
        wp_enqueue_script(
            'giggle-generator-js',
            plugin_dir_url(__FILE__) . 'giggle-generator.js',
            ['jquery'],
            '2.0.0',
            true
        );

        wp_localize_script(
            'giggle-generator-js', 'giggle_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('giggle_nonce'),
            ]
        );

        // Add inline CSS for styling
        wp_add_inline_style('admin-bar', self::get_custom_css());
    }

    /**
     * Get custom CSS
     */
    public static function get_custom_css()
    {
        return '
        .giggle-notice { animation: slideInRight 0.5s ease-out; }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .giggle-widget-content {
            padding: 15px;
        }

        .motivation-quote {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
            text-align: center;
        }

        .productivity-meter {
            margin-bottom: 15px;
        }

        .meter-bar {
            background: #e0e0e0;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .meter-fill {
            background: linear-gradient(90deg, #00c851, #ffbb33, #ff4444);
            height: 100%;
            transition: width 0.3s ease;
        }

        .mood-boost-btn {
            width: 100%;
            margin-top: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .mood-boost-result {
            margin-top: 10px;
            padding: 10px;
            background: #d4edda;
            border-radius: 5px;
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .giggle-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .giggle-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .big-stats {
            display: flex;
            justify-content: space-around;
        }

        .big-stat {
            text-align: center;
        }

        .big-stat .number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }

        .joke-display {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            min-height: 50px;
            display: none;
        }

        .achievement {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .achievement.unlocked {
            background: #d4edda;
            color: #155724;
        }

        .achievement.locked {
            background: #f8d7da;
            color: #721c24;
            opacity: 0.6;
        }
        ';
    }
}

// Initialize the plugin
if (is_admin()) {
    GiggleGenerator::init();
}

// Create JavaScript file content
if (!file_exists(plugin_dir_path(__FILE__) . 'giggle-generator.js')) {
    $js_content = "
jQuery(document).ready(function($) {
    // Mood boost button
    $('#mood-boost-btn').on('click', function() {
        var button = $(this);
        button.prop('disabled', true).text('🔄 Boosting...');

        $.ajax({
            url: giggle_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mood_boost',
                nonce: giggle_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#mood-boost-result').html(response.data.message).fadeIn();
                    button.text('✅ Boosted!').removeClass('button-primary').addClass('button-secondary');
                    setTimeout(function() {
                        button.text('🚀 Need Another Boost?').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
                        $('#mood-boost-result').fadeOut();
                    }, 3000);
                }
            },
            error: function() {
                button.text('❌ Try Again').prop('disabled', false);
            }
        });
    });

    // Test joke button
    $('#test-joke-btn').on('click', function() {
        var jokes = [
            '☕ Your code is so good, even bugs are impressed!',
            '🐱 A cat fixed your bug by walking on keyboard.',
            '🚀 Your WordPress is running faster than expected.',
            '🎪 Welcome to the circus! You\\'re the ringmaster.',
            '🧙‍♂️ Your database queries are so optimized, they bend spacetime.',
            '🎯 You hit ctrl+s so much, even autosave is jealous.',
            '🍕 Your code is like pizza - even when bad, still pretty good.',
            '🤖 Your functions are so efficient, even robots ask for tips.',
        ];

        var randomJoke = jokes[Math.floor(Math.random() * jokes.length)];
        $('#joke-display').html('<strong>🎭 ' + randomJoke + '</strong>').fadeIn();
    });

    // Add some fun interactions
    $('.giggle-card').hover(
        function() { $(this).css('transform', 'scale(1.02)'); },
        function() { $(this).css('transform', 'scale(1)'); }
    );
});
";

    file_put_contents(plugin_dir_path(__FILE__) . 'giggle-generator.js', $js_content);
}
