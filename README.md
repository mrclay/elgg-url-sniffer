# elgg-url-sniffer

Analyze a URL to see if it's within an Elgg site, is an action/page, likely represents a GUID, etc.

```php
// analyze a URL
$info = (new \UFCOE\Elgg\Url())->analyze($url);

// within the site? check this first!
$info->in_site;

$info->scheme_matches; // does the URL scheme match?
$info->host_matches; // does the URL host match?

// first URL segment within the site. E.g. "blog"
$info->identifier;

// array of URL segments after handler (e.g. ["view", "1234", "my-friendly-title"])
$info->handler_segments;

// URL corresponds to a GUID (otherwise 0)
$info->guid;

// URL corresponds to a container GUID (otherwise 0)
$info->container_guid;

// an action name (e.g. "profile/edit") or null
$info->action;

// a username, if a profile page or null
$info->username;
```
