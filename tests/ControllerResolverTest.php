<?php 

namespace Tests;

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
	 * @expectedException  			\InvalidArgumentException
	 * @expectedExceptionMessage	Unable to find controller "NonCallableController".
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
			'_controller' => 'Tests\TestController@sayHi',
			'firstName' => 'Jessie',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);
		$this->assertSame('Tests\TestController@sayHi', $request->attributes->get('_controller'));

		$controller = self::$resolver->getController($request);
		$this->assertTrue(is_callable($controller));

		$arguments = self::$resolver->getArguments($request, $controller);
		$this->assertSame('Hi Jessie Siat!', call_user_func_array($controller, $arguments));
	}

	/**
	 * @expectedException  \RuntimeException 
	 */
	public function testControllerHasNoDefaultArguments()
	{
		$parameters = array(
			'_controller' => 'Tests\TestController::sayHi',
		);
		$request = Request::create('/', 'GET');
		$request->attributes->add($parameters);

		$controller = self::$resolver->getController($request);
		$arguments = self::$resolver->getArguments($request, $controller);
	}
}

class TestController {

	public function sayHi($firstName, $lastName = 'Siat')
	{
		return 'Hi '.$firstName.' '.$lastName.'!';
	}

}