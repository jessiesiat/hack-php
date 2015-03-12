<?php 

namespace App\Controllers;

use Hack\Controller as BaseController;

class HelloController extends BaseController
{

	public function index()
	{
		return $this->render('index.html');
	}

}