<?php

namespace UFCOE\Elgg\Url;

class Result {

	/**
	 * Is the URL within the Elgg site?
	 *
	 * @var bool
	 */
	public $in_site = false;

	/**
	 * Does the URL match the scheme of Elgg's site URL?
	 *
	 * @var bool
	 */
	public $scheme_matches;

	/**
	 * Does the URL match the host of Elgg's site URL?
	 *
	 * @var bool
	 */
	public $host_matches;

	/**
	 * The entity's GUID, if the URL likely represent an entity on the Elgg site
	 *
	 * @var int
	 */
	public $guid = 0;

	/**
	 * The entity's GUID, if the URL likely represent a container entity on the Elgg site
	 *
	 * @var int
	 */
	public $container_guid = 0;

	/**
	 * The action name, if the URL likely represents an Elgg action
	 *
	 * @var string|null
	 */
	public $action = null;

	/**
	 * The username, if the URL likely represents an Elgg user
	 *
	 * @var string|null
	 */
	public $username = null;

	/**
	 * The first path segment if the URL is within Elgg
	 *
	 * @var string Empty string is the home page.
	 */
	public $identifier = null;

	/**
	 * The secondary path segments if the URL is within Elgg
	 *
	 * @var string[]
	 */
	public $handler_segments = [];
}
