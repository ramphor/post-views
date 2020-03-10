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
        add_action('ramphor_post_views_track_history_handle', array($this, 'track'), 10, 5);
    }

    public function track($newViews, $currentViews, $userId, $post, $userPostViews)
    {
        return $currentViews += 1;
    }

    public function count($post = null)
    {
        $post = ($post === null) ? $GLOBALS['post'] : get_post($post);
        if (!is_single() || !$this->checkPostType($post->post_type, $this->postTypes)) {
            return;
        }

        $userId = $this->trackUserView ? get_current_user_id() : 0;
        $userPostViews = Db::getUserViews(
            $userId,
            $post->ID,
            $post->post_type
        );
        $currentViews = 0;
        if (isset($userPostViews->views)) {
            $currentViews = (int)$userPostViews->views;
        }
        $views = apply_filters(
            'ramphor_post_views_track_history_handle',
            null,
            $currentViews,
            $userId,
            $post,
            $userPostViews,
            $this->postTypes
        );
        if ($views > 0 && Db::updateUserViewPost($userId, $post->ID, $views)) {
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
