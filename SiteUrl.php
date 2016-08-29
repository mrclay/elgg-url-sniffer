<?php

namespace UFCOE\Elgg;

class SiteUrl {

	protected $host;
	protected $scheme;
	protected $url_segments = array();

	/**
	 * Constructor
	 *
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
		$this->url_segments = ($path === '') ? array() : explode('/', $path);
	}

	/**
	 * Get a site path object if the URL is within the Elgg site, or false if not.
	 *
	 * @param string $url          URL, possibly within the site
	 * @param bool   $match_scheme Require the URL scheme to match, e.g. http: won't match https:
	 * @return SitePath|bool
	 */
	public function getSitePath($url, $match_scheme = true) {
		$ret = $this->analyzeUrl($url);

		if (!$ret || !$ret['path_within_site'] || ($ret['site_host'] !== $ret['host'])) {
			return false;
		}

		if ($match_scheme && ($ret['scheme'] !== $ret['site_scheme'])) {
			return false;
		}

		return new SitePath(implode('/', $ret['url_segments_within_site']));
	}

	/**
	 * Analyze a URL.
	 *
	 * @param string $url URL
	 * @return array|false
	 */
	public function analyzeUrl($url) {
		$url = trim($url);

		if (!preg_match('~^(https?)\\://([^/]+)(/.*|$)~', $url, $m)) {
			return false;
		}

		list (, $scheme, $host, $path) = $m;

		$path = self::normalizePath($path);

		$segments = ($path === '') ? array() : explode('/', $path);

		$ret = [
			'site_scheme' => $this->scheme,
			'site_host' => $this->host,
			'site_url_segments' => $this->url_segments,
			'host' => $host,
			'scheme' => $scheme,
			'path_within_site' => false,
			'url_segments' => $segments,
			'url_segments_within_site' => [],
		];

		$site_segments = $this->url_segments;
		while (count($site_segments)) {
			$site_segment = array_shift($site_segments);
			$segment = array_shift($segments);

			if ($segment !== $site_segment) {
				// not in site
				return $ret;
			}
		}

		$ret['path_within_site'] = true;
		$ret['url_segments_within_site'] = $segments;

		return $ret;
	}

	/**
	 * Trim and remove fragment/querystring from a path
	 *
	 * @param string $path URL path
	 * @return string
	 */
	public static function normalizePath($path) {
		// remove fragment/query
		list($path, ) = explode('#', $path, 2);
		list($path, ) = explode('?', $path, 2);

		return trim($path, '/ ');
	}
}
