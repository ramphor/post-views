<?php
namespace Ramphor\PostViews\Handlers;

use Ramphor\PostViews\Abstracts\HandlerAbstract;

class UserHandler extends HandlerAbstract
{
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
    }
}
