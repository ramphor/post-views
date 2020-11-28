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
    protected $guest_expire_time = 86400;

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

    public function setUserId($user_id = null) {
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

    /**
     * Expire time use to count user view.
     * When the view has expire time it is a new view.
     *
     * @param int $seconds
     */
    public function setGuestExpireTime($seconds = 86400)
    {
        $this->guest_expire_time = $seconds;
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

        $current_views = false;
        if ($this->tracking_history) {
            $current_views = DB::get_user_post_views($this->postId, $this->user_id);
        } else {
            $current_views = DB::get_user_post_views(
                $this->postId,
                $this->user_id,
                $this->user_ip,
                $this->user_id > 0 ? $this->expire_time : $this->guest_expire_time
            );
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
        } catch(Exception $e) {
        }
        return false;
    }

    public function isViewed($user_id = null) {
        if (is_null($user_id)) {
            $user_id = get_current_user_id();
        }
        return DB::get_user_post_views($this->postId, $user_id) > 0;
    }
}
