<?php

namespace Ramphor\PostViews;

class Counter
{
    protected $postTypes;
    protected $trackUserView;
    protected $trackHistory = true;

    public function __construct($postTypes, $trackUserView = false)
    {
        $this->postTypes = $postTypes;
        $this->trackUserView = $trackUserView;
    }

    public function register()
    {
        add_action('template_redirect', array($this, 'count'));
    }

    public function count()
    {
        if (!is_single() || !$this->checkPostType(get_post_type(), $this->postTypes)) {
            return;
        }
        $post = $GLOBALS['post'];
        $userId = $this->trackUserView ? 0 : get_current_user_id();
        $userPostViews = Db::getUserViews(
            $userId,
            $post->ID,
            $post->post_type
        );
        $views = 0;
        if (isset($userPostViews->views)) {
            $views = (int)$userPostViews->views;
        }
        if (apply_filters('ramphor_post_views_track_history_visited', $this->trackHistory, $this->postTypes)) {
            $views = apply_filters('ramphor_post_views_track_history_handle', $views + 1, $views, $this->postTypes);
        } else {
            $views++;
        }
        if (Db::updateUserViewPost($userId, $post->ID, $views)) {
            $this->update_post_meta($post->ID, $post->post_type);
        }
    }

    protected function checkPostType($postType, $allowPostTypes)
    {
        switch (gettype($allowPostTypes)) {
            case 'array':
                return in_array($postType, $allowPostTypes);
            default:
                return $postType === $allowPostTypes;
        }
    }

    public function update_post_meta($postId, $postType)
    {
        update_post_meta(
            $postId,
            Common::getPostMetaKey(),
            Db::getTotalPostViews($postId, $postType)
        );
    }
}
