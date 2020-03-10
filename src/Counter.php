<?php

namespace Ramphor\PostViews;

class Counter
{
    protected $postTypes;
    protected $trackUserView;

    public function __construct($postTypes, $trackUserView = false)
    {
        $this->postTypes = $postTypes;
        $this->trackUserView = $trackUserView;
    }

    public function register()
    {
    }

    public function count()
    {
    }

    public function update_post_meta()
    {
    }
}
