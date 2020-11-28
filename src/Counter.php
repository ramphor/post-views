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

    public function count() {
        add_action('template_redirect', array($this, '_count'));
    }

    public function _count() {
        if(is_single() && in_array(get_post_type(), $this->postTypes)) {
            global $post;
            foreach($this->handlers as $handler) {
                $handler->setPostId($post->ID);
                $handler->writeLog();
            }
        }
    }

    public function addHandle($handler) {
        if (is_a($handler, Handler::class)) {
            $this->handlers[] = $handler;
        }
    }

    public function getTotalPostViews() {
    }

    public function countTotalPostViews() {
    }

    public function updateTotalPostViews() {
    }
}
