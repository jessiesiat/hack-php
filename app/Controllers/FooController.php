<?php 

namespace App\Controllers;

class FooController extends BaseController
{
	
	public function __construct() 
	{
		// parent::__construct();
	}

	public function show()
	{
		$name = 'Foo';

		return $this->render('admin/foo.html', compact('name'));
	}

}