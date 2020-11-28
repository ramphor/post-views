<?php
namespace Ramphor\PostViews\Interfaces;

interface Handler {
    public function setPostId($postId);

    public function writeLog();
}
