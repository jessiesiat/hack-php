<?php 

namespace Tests;

use Hack\Foundation\Application;
use Hack\Controller;

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
		$controllerApp = Controller::getApplication();

		$this->assertInstanceOf('Hack\Foundation\Application', $controllerApp);
	}

	public function testCanCreateInstanceFromAbstractClass()
	{
		$app = new Application;

		$class = $app->make('Tests\AbstractClass', ['name' => 'Jessie']);
		$this->assertInstanceOf('Tests\AbstractClass', $class);
		$this->assertSame('Jessie', $class->getName());

		$class2 = $app->make('Tests\AbstractClass');
		$this->assertSame('Joe', $class2->getName());

		$class3 = $app->make('Tests\AbstractClass', ['Jessie']);
		$this->assertSame('Jessie', $class3->getName());

		$class4 = $app->make('Tests\AbstractClass', ['acme' => 'Jessie']);
		$this->assertSame('Joe', $class4->getName());
	}

}

class AbstractClass {

	protected $name;

	public function __construct($name = 'Joe')
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

}