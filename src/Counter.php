<?php
namespace Ramphor\PostViews;

use Ramphor\PostViews\Interfaces\Handler;

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
                $totalViews = $this->countTotalPostViews($post->ID);
                $this->updateTotalPostViews($totalViews + 1, $post->ID);
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
    }

    public function countTotalPostViews($post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_the_ID();
        }
    }

    public function updateTotalPostViews($total_view, $post_id = null)
    {
        if (is_null($post_id)) {
            $post_id = get_the_ID();
        }
    }
}
