<?php
namespace Ramphor\PostViews\Abstracts;

use Ramphor\PostViews\Interfaces\Handler;

abstract class HandlerAbstract implements Handler
{
    protected $postId;

    public function setPostId($postId)
    {
        $this->postId = $postId;
    }
}
