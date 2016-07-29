<?php

namespace UFCOE\Elgg;

use UFCOE\Elgg\Url\Result;

class Url {

	protected $host;
	protected $scheme;
	protected $segments = array();

	/**
	 * @param string $site_url if not given, will retrieve from elgg_get_site_url()
	 * @throws \InvalidArgumentException
	 */
	public function __construct($site_url = null) {
		if (!$site_url && is_callable('elgg_get_site_url')) {
			$site_url = elgg_get_site_url();
		}
		if (!preg_match('~^(https?)\\://([^/]+)(/.*|$)~', $site_url, $m)) {
			throw new \InvalidArgumentException('$siteUrl must be full URL');
		}
		list (, $this->scheme, $this->host, $path) = $m;

		$path = trim($path, '/');
		$this->segments = ($path === '') ? array() : explode('/', $path);
	}

	/**
	 * @param string $url URL to be analyzed
	 *
	 * @return Result|false False if cannot handle URL
	 */
	public function analyze($url) {
		$url = trim($url);

		// remove fragment/query
		list($url, ) = explode('#', $url, 2);
		list($url, ) = explode('?', $url, 2);

		if (!preg_match('~^(https?)\\://([^/]+)(/.*|$)~', $url, $m)) {
			return false;
		}
		list (, $scheme, $host, $path) = $m;

		$path = trim($path, '/');
		$segments = ($path === '') ? array() : explode('/', $path);

		$ret = new Result();
		$ret->scheme_matches = ($scheme === $this->scheme);
		$ret->host_matches = ($host === $this->host);
		if (!$ret->host_matches) {
			return $ret;
		}

		$site_segments = $this->segments;

		while (count($site_segments)) {
			$site_segment = array_shift($site_segments);
			$segment = array_shift($segments);

			if ($segment !== $site_segment) {
				// not in site
				return $ret;
			}
		}

		$ret->in_site = true;

		if (!$segments) {
			$ret->identifier = "";
			return $ret;
		}

		$ret->identifier = $segments[0];
		$ret->handler_segments = array_slice($segments, 1);

		if ($ret->identifier === 'action' && $ret->handler_segments) {
			$ret->action = implode('/', $ret->handler_segments);
			return $ret;
		}

		if ($ret->identifier === 'profile' && $ret->handler_segments) {
			$ret->username = urldecode($ret->handler_segments[0]);
			return $ret;
		}

		$site_path = implode('/', $segments);

		if ((count($segments) >= 3)
			&& in_array($segments[1], array('view', 'read'))
			&& preg_match('~^[1-9]\\d*$~', $segments[2])
		) {
			$ret->guid = (int)$segments[2];

		} elseif (preg_match('~^[^/]+/group/([1-9]\\d*)/all$~', $site_path, $m)) {
			// this is a listing of group items
			$ret->container_guid = (int) $m[1];

		} elseif (preg_match('~^[^/]+/add/([1-9]\\d*)$~', $site_path, $m)) {
			// this is a new item creation page
			$ret->container_guid = (int) $m[1];

		} elseif (preg_match('~^(?:[^/]+/)+([1-9]\\d*)(?:$|/)~', $site_path, $m)) {
			// less-reliable guessing (e.g. groups/profile/123)
			$ret->guid = (int) $m[1];
		}

		return $ret;
	}
}
