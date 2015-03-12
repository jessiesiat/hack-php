<?php 

use Hack\Foundation\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testApplicationInstance()
	{
		$app = new Application;

		$this->assertInstanceOf('Hack\Foundation\Application', $app);
		$this->assertInstanceOf('Pimple\Container', $app);
	}

	public function testApplicationContainer()
	{
		$app = new Application;
		$app['foo'] = 'bar';

		$this->assertSame('bar', $app['foo']);
		$this->assertTrue(isset($app['foo']));
	}

	public function testBaseControllerHasApplicationInstance()
	{
		$app = new Application;
		$controllerApp = Hack\Controller::getApplication();

		$this->assertInstanceOf('Hack\Foundation\Application', $controllerApp);
	}

}