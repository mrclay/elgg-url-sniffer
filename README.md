# elgg-url-sniffer

Analyze a URL to see if it's within an Elgg site, is an action/page, likely represents a GUID, etc.

```php
$sniffer = new \UFCOE\Elgg\Url();

// just guess GUID
$guid = $sniffer->getGuid($url);

// jsut guess container GUID
$container_guid = $sniffer->getContainerGuid($url);

// analyze the URL to the best of our ability
$info = $sniffer->analyze($url);

// some of these may be null
$info['scheme_matches']; // does the URL scheme match?
$info['host_matches']; // does the URL host match?
$info['guid'];
$info['container_guid'];
$info['action']; // an action name (e.g. "profile/edit")
$info['handler']; // string used to match a page handler. (e.g. "discussion")
$info['handler_segments']; // array of URL segments after handler (e.g. ["view", "1234", "my-friendly-title"])
```
