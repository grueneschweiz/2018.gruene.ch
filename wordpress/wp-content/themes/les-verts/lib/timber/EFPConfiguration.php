<?php

namespace SUPT;

use Timber\Post;
use Timber\Timber;

class EFPConfiguration extends Post {
    public function get_initial_action() {
        $engagement_funnel = new Engagement_Funnel();
        $action_id = $engagement_funnel->get_initial_action_id($this->id);
        
        return self::get_action_post($action_id);
    }

    public static function get_action_post($action_id) {
        if (!$action_id) {
            return null;
        }
        return Timber::get_post($action_id);
    }

    public function get_action_posts() {
        $posts = [];
        $action_ids = $this->get_field('actions');
        if (!$action_ids) {
            return $posts;
        }

        return Timber::get_posts($action_ids);
    }
}
