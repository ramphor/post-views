<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\Common;
use Ramphor\PostViews\Abstracts\HandlerAbstract;

class CookieHandler extends HandlerAbstract {
    protected $expire_time = 86400;

    /**
     * Expire time use to count user view.
     * When the view has expire time it is a new view.
     *
     * @param int $seconds
     */
    public function setExpireTime($seconds = 86400)
    {
        $this->expire_time = $seconds;
    }

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
            time() + $this->expire_time,
        );

        var_dump($_COOKIE);die;
    }

    public function isViewed() {
        $viewed_posts = isset($_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            ? explode('|', $_COOKIE[Common::POST_VIEWS_COOKIE_NAME])
            : array();
        return in_array($this->postId, $viewed_posts);
    }
}
