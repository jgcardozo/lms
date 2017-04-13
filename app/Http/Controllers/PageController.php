<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function support()
	{
		return view('lms.pages.support');
	}

	public function contact()
	{
		return view('lms.pages.contact');
	}
}
