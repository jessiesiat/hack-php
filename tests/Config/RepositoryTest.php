<?php 

namespace Tests\Config;

use Hack\Config\Repository as ConfigRepository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultValueOnUndefinedKey()
	{
		$repository = new ConfigRepository;

		$this->assertNull($repository->get('undefined_value'));
		$this->assertEquals($repository->get('foo', 'bar'), 'bar');
	}

	public function testConfigWithPassedValues()
	{
		$items = array(
				'foo' => 'bar',
				'acme' => array(
						'acne' => 'anan'
					),
			);
		$repository = new ConfigRepository($items);

		$this->assertEquals($repository->get('foo'), 'bar');
		$this->assertEquals($repository->get('acme.acne'), 'anan');
	}

	public function testConfigLoadedFromPath()
	{
		$repository = (new ConfigRepository)->loadFromPath(__DIR__.'/fixtures');

		$this->assertEquals($repository->get('config.debug'), true);
		$this->assertEquals($repository->get('config.timezone'), 'Asia/Manila');
	}

	public function testRepositoryImplementsArrayaccess()
	{
		$repository = new ConfigRepository;

		$repository['app.debug'] = true;
		$repository['app.timezone'] = 'UTC';

		$this->assertTrue($repository['app.debug']);
		$this->assertSame('UTC', $repository['app.timezone']);
		$this->assertFalse(isset($repository['app.locale']));
		$this->assertNull($repository['app.locale']);
	}

}