<?php
namespace Ramphor\PostViews;

class Common
{
    const POST_META_KEY = '_ramphor_post_views';

    final public static function getPostMetaKey()
    {
        return self::POST_META_KEY;
    }
}
