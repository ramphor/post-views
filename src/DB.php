<?php
namespace Ramphor\PostViews;

class DB
{
    public static function getTotalPostViews($postId, $postType = 'post')
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT SUM(views)
            FROM {$wpdb->prefix}ramphor_post_views v
            INNER JOIN {$wpdb->posts} p
                ON p.ID=v.post_id
            WHERE v.post_id=%d
                AND p.post_type=%s",
            $postId,
            $postType
        );

        return (int)$wpdb->get_var($sql);
    }

    public static function getUserViews($userId, $postId, $postType = null)
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT v.views,v.last_views
            FROM {$wpdb->prefix}ramphor_post_views v
            INNER JOIN {$wpdb->posts} p
            ON p.ID=v.post_id
            WHERE v.user_id=%d
                AND v.post_id=%d",
            $userId,
            $postId
        );

        if (!is_null($postType)) {
            $sql .= $wpdb->prepare(" AND p.post_type=%s", $postType);
        }

        return $wpdb->get_row($sql);
    }

    public static function checkUserViewPost($userId, $postId)
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT ID
            FROM {$wpdb->prefix}ramphor_post_views
            WHERE user_id=%d
                AND post_id=%d",
            $userId,
            $postId,
        );
        $viewId = $wpdb->get_var($sql);
        if ($viewId > 0) {
            return $viewId;
        }
        return false;
    }


    public static function insertUserViewPost($userId, $postId, $views)
    {
        global $wpdb;
        $sql = sprintf(
            "INSERT INTO {$wpdb->prefix}ramphor_post_views(user_id, post_id, views)
                VALUES(%d,%d,%d)",
            $userId,
            $postId,
            $views
        );
        if ($wpdb->query($sql)) {
            return $wpdb->insert_id;
        }
    }

    public static function updateUserViewPost($userId, $postId, $views)
    {
        if (self::checkUserViewPost($userId, $postId) === false) {
            return self::insertUserViewPost($userId, $postId, $views);
        }
        global $wpdb;
        $sql = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}ramphor_post_views
                SET views=%s
            WHERE user_id=%d
                AND post_id=%d",
            $views,
            $userId,
            $postId
        );

        return $wpdb->query($sql) !== false;
    }
}
