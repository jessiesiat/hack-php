<?php 

namespace App\Controllers;

use Hack\Controller as BaseController;
use Hack\Foundation\Application;

class HelloController extends BaseController
{

	public function index()
	{
		return $this->render('index.html');
	}

}