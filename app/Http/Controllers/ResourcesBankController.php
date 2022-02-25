<?php

namespace App\Http\Controllers;

use App\Models\ResourcesBank;
use Illuminate\Support\Facades\DB;

class ResourcesBankController extends Controller
{

    public function show($slug)
    {
        $resource = ResourcesBank::whereSlug($slug)->first();

        $raw = "select bank_id , child_id , rc.lft, rc.id, rc.title, rc.slug, rc.content from resourcechild_resourcebank rr
                    join resources_children rc on rc.id=rr.child_id
                    join resources_banks rb on rb.id=rr.bank_id
                where bank_id=$resource->id order by rc.lft asc"; 
        
        $sections = DB::select(DB::raw($raw));

        return view('lms.bonus.single-resource', ['resource' => $resource, 'sections'=>$sections]);
    }

} //class
