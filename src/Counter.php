<?php
namespace Ramphor\PostViews;

use Ramphor\PostViews\Interfaces\Handler;
use Ramphor\PostViews\Handlers\UserHandler;
use Ramphor\PostViews\Common;

class Counter
{
    protected $postTypes;
    protected $handlers = array();
    protected static $updatePostTotal = false;

    public function __construct($postTypes)
    {
        $this->postTypes = is_array($postTypes) ? $postTypes : array($postTypes);
    }

    public function count($post_id = null)
    {
        $post = !is_null($post_id) ? get_post($post_id) : get_post($GLOBALS['post']);

        if ($post && in_array($post->post_type, $this->postTypes)) {
            if (!static::$updatePostTotal) {
                $supportPostTypes = apply_filters(
                    'ramphor_post_views_post_types_apply_default_counter_handles',
                    array(
                        'post'
                    )
                );

                foreach ($supportPostTypes as $supportPostType) {
                    add_action("update_{$supportPostType}_total_views", array($this, 'updateTotalPostViews'), 10, 2);
                }
                static::$updatePostTotal = true;
            }

            $isNewView = false;
            $post_id   = $post->ID;

            foreach ($this->handlers as $handler) {
                $handler->setPostId($post_id);
                $result = $handler->writeLog();
                if (!$isNewView && $result) {
                    $isNewView = true;
                }
            }

            if ($isNewView) {
                do_action('ramphor_post_views_view_the_post', $post_id, $this->postTypes, $result);

                $totalViews = $this->countTotalPostViews($post_id);
                do_action("update_{$post->post_type}_total_views", $totalViews, $post_id);
            }
        }
    }

    public function addHandle($handler)
    {
        if (is_a($handler, Handler::class)) {
            $this->handlers[] = $handler;
        }
    }

    public function getTotalPostViews($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_the_ID();
        }

        $post_views = get_post_meta(
            $post_id,
            Common::POST_VIEWS_META_KEY,
            true
        );

        return intval($post_views);
    }

    public function countTotalPostViews($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_the_ID();
        }
        return DB::get_total_views($post_id);
    }

    public function updateTotalPostViews($total_view, $post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_the_ID();
        }
        update_post_meta(
            $post_id,
            Common::POST_VIEWS_META_KEY,
            intval($total_view)
        );
    }

    public function isViewed($post_id)
    {
        foreach ($this->handlers as $handler) {
            $handler->setPostId($post_id);
            if ($handler->isViewed()) {
                return true;
            }
        }
        return false;
    }
}
