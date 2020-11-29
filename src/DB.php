<?php
namespace Ramphor\PostViews;

class DB
{
    public static function get_user_post_views($post_id, $user_id = null, $ip = null, $expired_time = null)
    {
        global $wpdb;
        // When IP is setted this case is tracking user view so expire time and ip are condition
        if ($ip) {
            $sql = $wpdb->prepare(
                "SELECT pv.post_id, pv.views, h.client_ip, UNIX_TIMESTAMP(h.last_views) as last_views
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
            $record = $wpdb->get_row($sql);
            // When record is null this is new visitor so it is counted
            if (is_null($record)) {
                return static::get_user_post_views($post_id, $user_id);
            } else {
                // In the view is not expire we return false to doest not do any actions.
                if ($expired_time < $record->last_views) {
                    return false;
                }
                return intval($record->views);
            }
        } else {
            $sql = $wpdb->prepare(
                "SELECT *
                FROM {$wpdb->prefix}ramphor_post_views pv
                WHERE pv.post_id = %d
                    AND pv.user_id = %d",
                $post_id,
                $user_id
            );
            $record = $wpdb->get_row($sql);
            return is_null($record) ? 0 : $record->views;
        }
    }

    public static function check_is_viewed($post_id, $user_id = null)
    {
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

    public static function update_user_post_views($post_id, $views, $user_id = null)
    {
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

    public static function check_history_is_exists($client_ip, $post_id, $user_id = null)
    {
        global $wpdb;
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }
        $sql = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->prefix}ramphor_view_histories WHERE client_ip=%s AND post_id=%d AND user_id=%d",
            $client_ip,
            $post_id,
            $user_id
        );
        return intval($wpdb->get_var($sql));
    }

    public static function write_view_history($client_ip, $post_id, $user_id = null)
    {
        global $wpdb;
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }

        if (!static::check_history_is_exists($client_ip, $post_id, $user_id)) {
            return $wpdb->insert($wpdb->prefix . 'ramphor_view_histories', array(
                'client_ip' => $client_ip,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'last_views' => current_time('mysql', 1)
            ));
        } else {
            return $wpdb->update($wpdb->prefix . 'ramphor_view_histories', array(
                'last_views' => current_time('mysql', 1)
            ), array(
                'client_ip' => $client_ip,
                'post_id' => $post_id,
                'user_id' => $user_id,
            ));
        }
    }

    public static function get_total_views($post_id, $post_type = null)
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT SUM(views)
            FROM {$wpdb->prefix}ramphor_post_views v
            INNER JOIN {$wpdb->posts} p
                ON p.ID=v.post_id
            WHERE v.post_id=%d",
            $post_id
        );

        if (!is_null($post_type)) {
            $sql .= $wpdb->prepare(" AND p.post_type=%s", $post_type);
        }

        return (int)$wpdb->get_var($sql);
    }
}
