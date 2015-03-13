<?php 

namespace App\Controllers;

use Hack\Controller as BaseController;

class FooController extends BaseController
{
	
	public function __construct() 
	{
		// parent::__construct();
	}

	public function show()
	{
		$name = (new \App\Handlers\TestHandler)->getName();

		return $this->render('admin/foo.html', compact('name'));
	}

}