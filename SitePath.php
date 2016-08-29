<?php

namespace UFCOE\Elgg;

class SitePath {

	/**
	 * The $path exploded by /. E.g. http://example.com/elgg/foo/bar => ["foo", "bar"]
	 *
	 * @return string[]
	 */
	protected $url_segments = [];

	/**
	 * The entity's GUID, if the URL likely represent an entity on the Elgg site, else 0
	 *
	 * @var int
	 */
	protected $guid = 0;

	/**
	 * The entity's GUID, if the URL likely represent a container entity on the Elgg site, else 0
	 *
	 * @var int
	 */
	protected $container_guid = 0;

	/**
	 * The action name, if the URL likely represents an Elgg action, else ''
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 * The username, if the URL likely represents an Elgg user, else ''
	 *
	 * @var string
	 */
	protected $username = '';

	/**
	 * Constructor
	 *
	 * @param string $path Path within Elgg site. E.g. "blog/view/123/blog-title"
	 */
	public function __construct($path) {
		$path = SiteUrl::normalizePath($path);
		
		if ($path === '') {
			return;
		}
		
		$this->url_segments = explode('/', $path);
		$identifier = $this->url_segments[0];
		$handler_segments = array_slice($this->url_segments, 1);

		if ($identifier === 'action' && $handler_segments) {
			$this->action = implode('/', $handler_segments);
			return;
		}

		if ($identifier === 'profile' && $handler_segments) {
			$this->username = urldecode($handler_segments[0]);
			return;
		}

		if ((count($this->url_segments) >= 3)
			&& in_array($this->url_segments[1], array('view', 'read'))
			&& preg_match('~^[1-9]\\d*$~', $this->url_segments[2])
		) {
			$this->guid = (int)$this->url_segments[2];

		} elseif (preg_match('~^[^/]+/group/([1-9]\\d*)/all$~', $this->getPath(), $m)) {
			// this is a listing of group items
			$this->container_guid = (int) $m[1];

		} elseif (preg_match('~^[^/]+/add/([1-9]\\d*)$~', $this->getPath(), $m)) {
			// this is a new item creation page
			$this->container_guid = (int) $m[1];

		} elseif (preg_match('~^(?:[^/]+/)+([1-9]\\d*)(?:$|/)~', $this->getPath(), $m)) {
			// less-reliable guessing (e.g. groups/profile/123)
			$this->guid = (int) $m[1];
		}
	}

	/**
	 * Path within the Elgg site, with no trailing/leading slashes. Empty string is home page.
	 * E.g. http://example.com/elgg/foo/bar => "foo/bar"
	 *
	 * @return string
	 */
	public function getPath() {
		return implode('/', $this->url_segments);
	}

	/**
	 * The path split by "/". E.g. http://example.com/elgg/foo/bar => ["foo", "bar"]
	 *
	 * @return string[]
	 */
	public function getUrlSegments() {
		return $this->url_segments;
	}

	/**
	 * The entity's GUID, if the URL likely represents an entity, else 0
	 *
	 * @return int
	 */
	public function getGuid() {
		return $this->guid;
	}

	/**
	 * The entity's GUID, if the URL likely represents a container entity, else 0
	 *
	 * @return int
	 */
	public function getContainerGuid() {
		return $this->container_guid;
	}

	/**
	 * The action name, if the URL likely represents an Elgg action, else ''
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * The username, if the URL likely represents an Elgg user, else ''
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Is this path within the given path?
	 *
	 * E.g. if the current path is "blog/view/123", this would return true for "blog",
	 * but false for "bl" or "blog/vi".
	 *
	 * @param string $path Path to compare. E.g. "blog/view"
	 *
	 * @return bool
	 */
	public function isWithinPath($path) {
		$path = SiteUrl::normalizePath($path);

		$current_path = $this->getPath() . '/';
		$path = trim($path, '/') . '/';

		if ($path === '/') {
			return true;
		}

		return (0 === strpos($current_path, $path));
	}
}
