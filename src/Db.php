<?php
namespace Ramphor\PostViews;

class Db
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

        return (int)$wpdb->get_val($sql);
    }

    public static function getUserViews($userId, $postId, $postType = 'post')
    {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT v.views,v.last_views
			FROM {$wpdb->prefix}ramphor_post_views v
			INNER JOIN {$wpdb->posts} p
			ON p.ID=v.post_id
			WHERE v.user_id=%d
				AND v.post_id=%d
				AND p.post_type=%s",
            $userId,
            $postId,
            $postType
        );

        return $wpdb->get_row($sql);
    }

    public static function updateUserViewPost($userId, $postId, $views)
    {
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
