<?php
use Ramphor\PostViews\Common;

if (!function_exists('ramphor_get_post_views')) {
    function ramphor_get_post_views($post_id = null)
    {
        if (is_null($post_id)) {
            global $post;
            $post_id = $post->ID;
        }
        return intval(get_post_meta($post_id, Common::POST_VIEWS_META_KEY, true));
    }
}

// Create alias for function ramphor_get_post_views
if (!function_exists('get_post_views')) {
    function get_post_views($post_id = null)
    {
        return ramphor_get_post_views($post_id);
    }
}
