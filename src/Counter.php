<?php
namespace Ramphor\PostViews;

use Ramphor\PostViews\Interfaces\Handler;
use Ramphor\PostViews\Handlers\UserHandler;
use Ramphor\PostViews\Common;

class Counter
{
    protected $postTypes;
    protected $handlers = array();

    public function __construct($postTypes)
    {
        $this->postTypes = $postTypes;
    }

    public function count()
    {
        add_action('template_redirect', array($this, '_count'));
    }

    public function _count()
    {
        if (is_single() && in_array(get_post_type(), $this->postTypes)) {
            global $post;
            $isNewView = false;

            foreach ($this->handlers as $handler) {
                $handler->setPostId($post->ID);
                $result = $handler->writeLog();
                if (!$isNewView && $result) {
                    $isNewView = true;
                }
            }

            if ($isNewView) {
                do_action('ramphor_post_views_view_the_post', $post->ID, $this->postTypes, $result);
                $this->updateTotalPostViews(
                    $this->countTotalPostViews($post->ID),
                    $post->ID
                );
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
            true,
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

    public function isViewed($post_id) {
        foreach($this->handlers as $handler) {
            $handler->setPostId($post_id);
            if ($handler->isViewed()) {
                return true;
            }
        }
        return false;
    }
}
