<?php 

namespace App\Controllers;

class HelloController extends BaseController
{

	public function index()
	{
		return $this->render('index.html');
	}

}