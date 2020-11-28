<?php
namespace Ramphor\PostViews;

class Setup
{
    public function createTables()
    {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ramphor_post_views(
            ID BIGINT NOT NULL AUTO_INCREMENT,
            user_id BIGINT NOT NULL DEFAULT 0,
            post_id BIGINT NOT NULL,
            views BIGINT NOT NULL DEFAULT 0,
            last_views TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(ID)
        )";
        $wpdb->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ramphor_view_histories(
            client_ip BIGINT NOT NULL DEFAULT 0,
            post_id BIGINT NOT NULL,
            user_id BIGINT NULL DEFAULT 0,
            last_views TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(client_ip)
        )";
        $wpdb->query($sql);
    }
}
