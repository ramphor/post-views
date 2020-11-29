<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\DB;
use Ramphor\PostViews\Common;
use Ramphor\PostViews\Abstracts\HandlerAbstract;

class UserHandler extends HandlerAbstract
{
    protected $user_id;
    protected $user_ip;

    protected $expire_time = 86400;

    protected $tracking_history = false;

    public function __construct($tracking_history = false)
    {
        if ($tracking_history) {
            $this->enableTrackingViewHistory();
        }
    }

    public function setRemoteIP($ip)
    {
        $this->user_ip = $ip;
    }

    public function setUserId($user_id = null)
    {
        if (is_null($user_id)) {
            $this->user_id = get_current_user_id();
        } else {
            $this->user_id = $user_id;
        }
    }

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

    public function enableTrackingViewHistory()
    {
        $this->tracking_history = true;
    }

    public function writeLog()
    {
        if (!$this->postId) {
            return;
        }

        /**
         * Current view has 2 cases
         *
         * false: Current user is viewed and skip count the view
         * number: Current user has expire view or not view post brefore so we will count this view
         *
         * @var boolean|int
         */
        $current_views = false;
        if ($this->tracking_history) {
            $current_views = DB::get_user_post_views(
                $this->postId,
                $this->user_id,
                $this->user_ip,
                $this->expire_time
            );
        } else {
            $current_views = DB::get_user_post_views($this->postId, $this->user_id);
        }

        if ($current_views === false) {
            return false;
        }

        $views = apply_filters(
            'ramphor_post_views',
            $current_views + 1,
            $this->user_id,
            $this->user_ip
        );

        try {
            DB::update_user_post_views($this->postId, $views, $this->user_id);
            if ($this->tracking_history && $this->user_ip) {
                DB::write_view_history($this->user_ip, $this->postId, $this->user_id);
            }
            return $views;
        } catch (Exception $e) {
        }
        return false;
    }

    public function isViewed($user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }

        return DB::get_user_post_views(
            $this->postId,
            $user_id,
            $this->tracking_history ? $this->user_ip : null,
            $this->expire_time
        ) > 0;
    }
}
