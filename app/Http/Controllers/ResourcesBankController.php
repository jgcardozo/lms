<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResourcesBank;

class ResourcesBankController extends Controller
{
    
    public function show($slug)
    {
        $resource = ResourcesBank::whereSlug($slug)->first();

        return view('lms.bonus.single-resource', ['resource' => $resource]);
    }


}//class
