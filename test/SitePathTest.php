<?php

namespace UFCOE\Elgg;

class SitePathTest extends \PHPUnit_Framework_TestCase {

	public function testSegments() {
		$data = array(
			'' => [],
			'/' => [],
			'h' => ['h'],
			'/h//f?123' => ['h', '', 'f'],
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getUrlSegments());
		}
	}

	public function testPath() {
		$data = array(
			'' => '',
			'/' => '',
			'h' => 'h',
			'/h/f?123' => 'h/f',
			'/h//f?123' => 'h//f',
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getPath());
		}
	}

	public function testGuid() {
		$data = array(
			'' => 0,
			'/h' => 0,
			'/h/123?234' => 123,
			'/h/view/023/not-real-guid' => 0,
			'/h/foo/12-4/123/hello' => 123,
			'/profile/123' => 0,
			'/file/view/123/345' => 123,
			'/groups/profile/123/hello' => 123,
			'/file/group/123/all' => 0,
			'/file/add/123' => 0,
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getGuid());
		}
	}

	public function testContainerGuid() {
		$data = array(
			'' => 0,
			'/h' => 0,
			'/h/123?234' => 0,
			'/h/view/023/not-real-guid' => 0,
			'/h/foo/12-4/123/hello' => 0,
			'/profile/123' => 0,
			'/file/view/123/345' => 0,
			'/groups/profile/123/hello' => 0,
			'/file/group/123/all' => 123,
			'/file/add/123' => 123,
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getContainerGuid());
		}
	}

	public function testAction() {
		$data = array(
			'action/foo/bar' => 'foo/bar',
			'action' => '',
			'action/' => '',
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getAction());
		}
	}

	public function testUsername() {
		$data = array(
			'http://example.org/base' => '',
			'/profile/' => '',
			'/profile/foo' => 'foo',
			'/profile/f%3Doo' => 'f=oo',
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, (new SitePath($path))->getUsername());
		}
	}

	public function testIsWithinSite() {
		$site_path = new SitePath('blog/view/1234/my-title');
		$data = array(
			'blog' => true,
			'blog/view' => true,
			'blo' => false,
			'' => true,
		);
		foreach ($data as $path => $val) {
			$this->assertSame($val, $site_path->isWithinPath($path));
		}
	}
}
