<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\Abstracts\HandlerAbstract;

class UserHandler extends HandlerAbstract
{
    protected $user_id;

    protected $enable_guest = false;
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
