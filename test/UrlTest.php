<?php

use UFCOE\Elgg\Url;
use UFCOE\Elgg\Url\Result;

class ElggUrlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Url
	 */
	protected $url;

	public function setUp() {
		$this->url = new Url('http://example.org/base/path/');
	}

	protected function analyze($url) {
		return $this->url->analyze($url);
	}

	public function testInvalidUrl() {
		$this->assertFalse($this->analyze('ftp://example.org/'));
		$this->assertInstanceOf(Result::class, $this->analyze('http://example.org'));

		$this->setExpectedException('InvalidArgumentException');
		new Url('ftp://example.org/');
	}

	public function testInSite() {
		$data = array(
			'http://example.org/base/pat' => false,
			'http://example.org/base/path' => true,
			'http://example.org/base/path/' => true,
			'https://example.org/base/path/foo' => true,
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->in_site);
		}
	}

	public function testAction() {
		$data = array(
			'http://example.org/base/path' => null,
			'https://example.org/base/path/action/foo/bar' => 'foo/bar',
			'https://example.org/base/path/action' => null,
			'http://example.org/base/path/action/' => null,
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->action);
		}
	}

	public function testIdentifier() {
		$data = array(
			'http://example.org/base' => null,
			'http://example.org/base/path' => '',
			'http://example.org/base/path/' => '',
			'https://example.org/base/path/h' => 'h',
			'https://example.org/base/path/h/f?123' => 'h',
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->identifier);
		}
	}

	public function testSegments() {
		$data = array(
			'http://example.org/base/path' => array(),
			'https://example.org/base/path/h' => array(),
			'https://example.org/base/path/h/foo' => array('foo'),
			'https://example.org/base/path/h/foo/ba?re' => array('foo', 'ba'),
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->handler_segments);
		}
	}

	public function testGuid() {
		$data = array(
			'http://example.org/base/path' => 0,
			'https://example.org/base/path/h' => 0,
			'https://example.org/base/path/h/123?234' => 123,
			'https://example.org/base/path/h/view/023/not-real-guid' => 0,
			'https://example.org/base/path/h/foo/12-4/123/hello' => 123,
			'https://example.org/base/path/profile/123' => 0,
			'http://example.org/base/path/file/view/123/345' => 123,
			'http://example.org/base/path/groups/profile/123/hello' => 123,
			'http://example.org/base/path/file/group/123/all' => 0,
			'http://example.org/base/path/file/add/123' => 0,
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->guid);
		}
	}

	public function testContainerGuid() {
		$data = array(
			'http://example.org/base/path' => 0,
			'https://example.org/base/path/h' => 0,
			'https://example.org/base/path/h/123?234' => 0,
			'https://example.org/base/path/h/view/023/not-real-guid' => 0,
			'https://example.org/base/path/h/foo/12-4/123/hello' => 0,
			'https://example.org/base/path/profile/123' => 0,
			'http://example.org/base/path/file/view/123/345' => 0,
			'http://example.org/base/path/groups/profile/123/hello' => 0,
			'http://example.org/base/path/file/group/123/all' => 123,
			'http://example.org/base/path/file/add/123' => 123,
		);
		foreach ($data as $url => $val) {
			$url = $this->analyze($url);
			$this->assertSame($val, $url->container_guid);
		}
	}
}
