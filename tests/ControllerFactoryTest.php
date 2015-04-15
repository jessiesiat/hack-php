<?php 

use Hack\ControllerFactory;
use Symfony\Component\Routing\RouteCollection;

class ControllerFactoryTest extends \PHPUnit_Framework_TestCase
{

	protected $factory;

	public function setUp()
	{
		$this->factory = new ControllerFactory;
	}

	public function testMatchController()
	{
		$callback = function() {
			return 'Hi';
		};

		$this->factory->match('GET', '/foo', $callback);
		$this->factory->match('POST', '/foo', $callback);

		$this->assertCount(2, $this->factory->flush());
	}

}


