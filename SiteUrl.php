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
	 * @param string $url                  URL, possibly within the site
	 * @param bool   $require_scheme_match To match, the scheme must match (e.g. http: won't match https:)
	 * @return SitePath|bool
	 */
	public function getSitePath($url, $require_scheme_match = true) {
		$url = trim($url);

		if (!preg_match('~^(https?)\\://([^/]+)(/.*|$)~', $url, $m)) {
			return false;
		}

		list (, $scheme, $host, $path) = $m;

		$path = self::normalizePath($path);
		$segments = ($path === '') ? array() : explode('/', $path);

		$scheme_matches = ($scheme === $this->scheme);
		if ($require_scheme_match && !$scheme_matches) {
			return false;
		}

		$host_matches = ($host === $this->host);
		if (!$host_matches) {
			return false;
		}

		while (count($this->url_segments)) {
			$site_segment = array_shift($this->url_segments);
			$segment = array_shift($segments);

			if ($segment !== $site_segment) {
				// not in site
				return false;
			}
		}

		return new SitePath(implode('/', $segments));
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
