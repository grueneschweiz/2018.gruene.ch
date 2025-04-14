<?php

namespace SUPT;

use Timber\Timber;
use function add_filter;
use function file_exists;
use function get_page_template_slug;
use function get_posts;
use function is_front_page;

class Frontpage_controller {
    public static function register() {
        add_filter('timber/context', array(__CLASS__, 'add_to_context'));
    }

    public static function add_to_context($context) {
        if (is_front_page() || 'single_front.php' === get_page_template_slug()) {
            $context['post'] = Timber::get_post();
            
            // Initialize with empty values to prevent null context
            $context['latest_press_release'] = null;
            $context['latest_posts'] = [];
            
            if ($context['post']) {
                $context['latest_press_release'] = self::get_latest_press_release($context);
                $context['latest_posts'] = self::get_latest_posts($context);
                $context['no_sanuk'] = !file_exists(WP_CONTENT_DIR . '/sanuk/font.ttf');
                self::add_events_details($context);
            }
        }

        return $context;
    }

    private static function get_latest_press_release(&$context) {
        if (empty($context['post']->custom['content_blocks'])) {
            return null;
        }

        foreach ($context['post']->custom['content_blocks'] as $id => $type) {
            if ('media' == $type) {
                // get post category
                $id = (int)$id;
                $cat_id = $context['post']->custom["content_blocks_{$id}_category"];

                if (!$cat_id) {
                    continue;
                }

                // get latest post query
                $args = array(
                    'posts_per_page' => 1,
                    'cat' => $cat_id,
                    'post_status' => 'publish', // prevent 'private' if logged in
                    'meta_query' => array(
                        array(
                            'key' => 'settings_show_on_front_page',
                            'value' => '1',
                        )
                    ),
                );

                // get the latest post
                $press_posts = Timber::get_posts($args);

                if (!empty($press_posts)) {
                    return $press_posts[0];
                }
            }
        }

        return null;
    }

    private static function get_latest_posts($context) {
        if (empty($context['post']->custom['content_blocks'])) {
            return [];
        }

        $configs = [];
        foreach ($context['post']->custom['content_blocks'] as $id => $type) {
            if ($type !== 'single' && $type !== 'double') {
                continue;
            }

            $configs = array_merge($configs, self::get_post_config_selectors($id, $type));
        }

        $loaded_posts = [];
        if ($context['latest_press_release']) {
            $loaded_posts[] = $context['latest_press_release']->id;
        }

        $latest_posts = [];
        foreach ($configs as $config) {
            if (empty($config)) {
                continue;
            }

            $args = array(
                'posts_per_page' => 1,
                'cat' => $config['cat_id'],
                'post_status' => 'publish',
                'post__not_in' => $loaded_posts,
                'meta_query' => array(
                    array(
                        'key' => 'settings_show_on_front_page',
                        'value' => '1',
                    )
                ),
            );

            $posts = Timber::get_posts($args);
            if (!empty($posts)) {
                $latest_posts[] = $posts[0];
                $loaded_posts[] = $posts[0]->id;
            }
        }

        return $latest_posts;
    }

    private static function get_post_config_selectors($id, $type) {
        if ('single' === $type) {
            return array(
                array(
                    'selection' => "content_blocks_{$id}_post_selection",
                    'post' => "content_blocks_{$id}_post",
                    'category' => "content_blocks_{$id}_category",
                    'id' => array($id, 0),
                    'cat_id' => "content_blocks_{$id}_category",
                )
            );
        } else {
            return array(
                array(
                    'selection' => "content_blocks_{$id}_post_1_post_selection",
                    'post' => "content_blocks_{$id}_post_1_post",
                    'category' => "content_blocks_{$id}_post_1_category",
                    'id' => array($id, 0),
                    'cat_id' => "content_blocks_{$id}_post_1_category",
                ),
                array(
                    'selection' => "content_blocks_{$id}_post_2_post_selection",
                    'post' => "content_blocks_{$id}_post_2_post",
                    'category' => "content_blocks_{$id}_post_2_category",
                    'id' => array($id, 1),
                    'cat_id' => "content_blocks_{$id}_post_2_category",
                ),
            );
        }
    }

    private static function add_events_details(&$context) {
        if (empty($context['post']->custom['content_blocks'])) {
            return;
        }

        foreach ($context['post']->custom['content_blocks'] as $id => $type) {
            if ('events' === $type) {

                $post_per_page = (int)$context['post']->{"content_blocks_{$id}_max_num"};
                $category = $context['post']->{"content_blocks_{$id}_event_category"};
                $has_category = 'category' === $context['post']->{"content_blocks_{$id}_show"};

                // get upcoming and running events
                $args = array(
                    'post_type' => 'tribe_events',
                    'posts_per_page' => $post_per_page,
                    'post_status' => 'publish', // prevent 'private' if logged in
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'meta_key' => '_EventStartDate',
                    'meta_query' => array(
                        array(
                            'key' => '_EventEndDate',
                            'compare' => '>',
                            'value' => date('Y-m-d H:i:s'),
                            'type' => 'DATETIME'
                        )
                    ),
                );

                $category = empty($category) ? null : $category;

                if ($has_category && $category) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'tribe_events_cat',
                            'field' => 'term_id',
                            'terms' => $category,
                        )
                    );
                }

                $context['events'] = new SUPTPostQuery($args);
                $context['events_link'] = tribe_get_listview_link($category);

                // the events block is limited to one, so we're done now
            }
        }
    }
}
