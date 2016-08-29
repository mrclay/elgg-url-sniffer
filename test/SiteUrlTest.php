<?php

namespace UFCOE\Elgg;

class SiteUrlTest extends \PHPUnit_Framework_TestCase {

	const SITE_URL = 'http://example.org/base/path/';

	/**
	 * @var SiteUrl
	 */
	protected $url;

	public function setUp() {
		$this->url = new SiteUrl(self::SITE_URL);
	}

	public function testInvalidUrlReturnsFalse() {
		$this->assertFalse($this->url->getSitePath('ftp://example.org/'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidSiteUrlThrows() {
		new SiteUrl('ftp://example.org/');
	}

	public function testGetPathReturnsPathForElggUrls() {
		$this->assertFalse($this->url->getSitePath('http://example.org/base/'));
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('http://example.org/base/path'));
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('http://example.org/base/path/'));
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('http://example.org/base/path/foo'));
		$this->assertFalse($this->url->getSitePath('https://example.org/base/path'));
		$this->assertFalse($this->url->getSitePath('https://example.org/base/path/'));
		$this->assertFalse($this->url->getSitePath('https://example.org/base/path/foo'));
	}

	public function testGetPathCanAcceptOtherScheme() {
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('https://example.org/base/path', false));
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('https://example.org/base/path/', false));
		$this->assertInstanceOf(SitePath::class, $this->url->getSitePath('https://example.org/base/path/foo', false));
	}

	/**
	 * @dataProvider samePathProvider
	 */
	public function testGetPathReturnsSamePath($url, $path) {
		$this->assertSame($path, $this->url->getSitePath($url)->getPath());
	}

	function samePathProvider() {
		return [
			['http://example.org/base/path', ''],
			['http://example.org/base/path/', ''],
			['http://example.org/base/path//', ''],
			['http://example.org/base/path/foo', 'foo'],
			['http://example.org/base/path/foo/bar?/baz', 'foo/bar'],
			['http://example.org/base/path/foo/bar?/baz#/bang', 'foo/bar'],
			['http://example.org/base/path/foo//bar/', 'foo//bar'],
		];
	}
}
