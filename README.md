# post-views
Track visited posts and user view posts

# install
```
composer require ramphor/post-views
```

```
$userHandler = new UserHandler(true);
$userHandler->setRemoteIP(wordland_get_real_ip_address());
$userHandler->setUserId(get_current_user_id());
$userHandler->setExpireTime(1 * 24 * 60 * 60); // 1 day

$cookieHandler = new CookieHandler();
$cookieHandler->setExpireTime(30 * 24 * 60 * 60); // 30 days

$this->viewCounter = new PostViewCounter(PostTypes::get());
$this->viewCounter->addHandle($cookieHandler);
$this->viewCounter->addHandle($userHandler);

$this->viewCounter->count();
```
