<?php
namespace Ramphor\PostViews;

class DB
{
    public static function get_user_post_views($post_id, $user_id = null, $ip = null, $expired_time = null) {
        global $wpdb;
        if ($ip) {
            $sql = $wpdb->prepare(
                "SELECT pv.post_id, pv.views, h.client_ip, h.last_views
                FROM {$wpdb->prefix}ramphor_post_views pv
                    LEFT JOIN {$wpdb->prefix}ramphor_view_histories h
                    ON pv.post_id = h.post_id
                WHERE pv.post_id = %d
                    AND pv.user_id = %d
                    AND h.client_ip = %s
                ",
                $post_id,
                $user_id,
                $ip
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT *
                FROM {$wpdb->prefix}ramphor_post_views pv
                WHERE pv.post_id = %d
                    AND pv.user_id = %d",
                $post_id,
                $user_id
            );
        }
        $record = $wpdb->get_row($sql);
    }

    public static function check_is_viewed($post_id, $user_id = null) {
        global $wpdb;
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }
        $sql = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}ramphor_post_views WHERE post_id=%d AND user_id=%d",
            $post_id,
            $user_id
        );
        return intval($wpdb->get_var($sql));
    }

    public static function update_user_post_views($post_id, $views, $user_id = null) {
        global $wpdb;
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }
        if (!static::check_is_viewed($post_id, $user_id)) {
            $wpdb->insert($wpdb->prefix . 'ramphor_post_views', array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'views' => $views,
                'last_views' => current_time('mysql', 1)
            ));
        } else {
            $wpdb->update($wpdb->prefix . 'ramphor_post_views', array(
                'views' => $views,
                'last_views' => current_time('mysql', 1)
            ), array(
                'post_id' => $post_id,
                'user_id' => $user_id
            ));
        }
    }

    public static function check_history_is_exists($client_ip, $post_id) {
    }

    public static function write_view_history($client_ip, $post_id, $user_id = null) {
    }

    public static function get_total_views($post_id) {
    }
}
