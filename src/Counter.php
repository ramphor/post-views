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
    }

    public function addHandle($handler) {
        if ($handler instanceof Handler) {
            array_push($this->handlers, $handler);
        }
    }

    public function getTotalPostViews() {
    }

    public function countTotalPostViews() {
    }

    public function updateTotalPostViews() {
    }
}
