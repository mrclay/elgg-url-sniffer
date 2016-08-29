# elgg-url-sniffer

Analyze a URL to see if it's within an Elgg site, is an action/page, likely represents a GUID, etc.

```php
$site_url = new \UFCOE\Elgg\SiteUrl('http://example.org/my-elgg-site/');

$path = $site_url->getSitePath($given_url);
if (!$path) {
    // URL is not within Elgg site
    // e.g. 'https://example.org/my-elgg-site/' (scheme mismatch)
    // e.g. 'http://example.com/my-elgg-site/' (host mismatch)
    // e.g. 'http://example.org/other-path' (not in site path)
    return;
}

// let's say URL was http://example.org/my-elgg-site/blog/view/1234/my-friendly-title

// URL segments within the site. ["blog", "view", "1234", "my-friendly-title"]
$path->getUrlSegments();

// path within the site. Here "blog/view/1234/my-friendly-title"
$path->getPath();

// URL corresponds to a GUID. Here 1234
$path->getGuid();

// URL corresponds to a container GUID. Here 0 (unknown)
$path->getContainerGuid();

// an action name. e.g. "profile/edit", Here null
$path->getAction();

// a username if a profile page or null
$path->getUsername();

// test whether the path is within a given one
$path->isWithinPath("blog"); // is any blog view page, so true
$path->isWithinPath("blo");  // first URL segment must be "blo", so false
$path->isWithinPath("blog/view");  // is any blog view page, so true
```
