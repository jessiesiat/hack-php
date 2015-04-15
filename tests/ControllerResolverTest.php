<?php 

use Hack\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolverTest extends \PHPUnit_Framework_TestCase
{
	protected static $resolver;

	public static function setUpBeforeClass()
	{
		self::$resolver = new ControllerResolver;
	}

	/**
	 * @expectedException  			\InvalidArgumentException
	 * @expectedExceptionMessage	Class "FooController" does not exist.
	 */
	public function testNonExistentController()
	{
		$parameters = array(
			'_controller' => 'FooController@sayHi',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);

		$controller = self::$resolver->getController($request);
	}

	/**
	 * @expectedException  		  \InvalidArgumentException
	 * @expectedExceptionMessage  Unable to find controller "NonCallableController".
	 */
	public function testNonCallableController()
	{
		$parameters = array(
			'_controller' => 'NonCallableController',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);

		$controller = self::$resolver->getController($request);
	}

	public function testCanGetControllerFromRequest()
	{
		$parameters = array(
			'_controller' => 'TestController@sayHi',
			'name' => 'Jessie',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);
		$this->assertSame('TestController@sayHi', $request->attributes->get('_controller'));

		$controller = self::$resolver->getController($request);
		$this->assertTrue(is_callable($controller));

		$arguments = self::$resolver->getArguments($request, $controller);
		$this->assertSame('Hi Jessie!', call_user_func_array($controller, $arguments));
	}

	/**
	 * @expectedException  		  \RuntimeException
	 * @expectedExceptionMessage  Controller "TestController::sayHi()" requires that you provide a value
	 */
	public function testControllerHasNoDefaultArguments()
	{
		$parameters = array(
			'_controller' => 'TestController::sayHi',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);

		$controller = self::$resolver->getController($request);
		$arguments = self::$resolver->getArguments($request, $controller);
	}
}

class TestController {

	public function sayHi($name)
	{
		return 'Hi '.$name.'!';
	}

}