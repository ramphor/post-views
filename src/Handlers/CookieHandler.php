<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\Common;
use Ramphor\PostViews\Abstracts\HandlerAbstract;

class CookieHandler extends HandlerAbstract {
    public function writeLog() {
        $viewed_posts = isset($_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            ? explode('|', $_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            : array();

        if (!in_array($this->postId, $viewed_posts)) {
            array_push($viewed_posts, $this->postId);
        }
        setcookie(
            Common::POST_VIEWS_COOKIE_NAME,
            implode('|', $viewed_posts),
            time() + $this->guest_expire_time
        );
    }

    public function isViewed() {
        $viewed_posts = isset($_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            ? explode('|', $_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            : array();
        return in_array($this->postId, $viewed_posts);
    }
}
