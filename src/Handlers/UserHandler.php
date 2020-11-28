<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\Abstracts\HandlerAbstract;

class UserHandler extends HandlerAbstract
{
    protected $user_id;
    protected $user_ip;
    protected $expire_time;
    protected $guest_expire_time;

    protected $enable_guest = false;
    protected $track_guest_cookie = false;
    protected $tracking_history = false;

    public function __construct($enable_guest_user = false, $tracking_history = false)
    {
        if ($enable_guest_user) {
            $this->enableGuest();
        }
        if ($tracking_history) {
            $this->enableTrackingViewHistory();
        }
    }

    public function enableGuest()
    {
        $this->enable_guest = true;
    }

    public function trackGuestCookie()
    {
        $this->track_guest_cookie = true;
    }

    public function setRemoteIP($ip)
    {
        $this->user_ip = $ip;
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
    public function setGuestExpireTime($seconds = 86400, $track_guest_cookie = false)
    {
        $this->guest_expire_time = $seconds;
        if ($track_guest_cookie) {
            $this->trackGuestCookie();
        }
    }

    public function enableTrackingViewHistory()
    {
        $this->tracking_history = true;
    }

    public function writeLog()
    {
        if (!is_user_logged_in()) {
            // If user is guest and counter is not allow guest user the module is return
            if (!$this->enable_guest) {
                return false;
            }
            $this->user_id = 0;
        } else {
            $this->user_id = get_current_user_id();
        }
    }
}
