<?php

namespace App\Http\Controllers;

use App\Models\ResourcesBank;
use Illuminate\Support\Facades\DB;

class ResourcesBankController extends Controller
{

    public function show($slug)
    {
        $resource = ResourcesBank::whereSlug($slug)->first();
        if ($resource->is_locked) {
            return \Redirect::to('/');
        }

          
        $raw = "select resources_bank_id , resources_child_id , rc.lft, rc.id, rc.title, rc.slug, rc.content, rc.published from resourcechild_resourcebank rr
                    join resources_children rc on rc.id=rr.resources_child_id
                    join resources_banks rb on rb.id=rr.resources_bank_id
                where resources_bank_id=$resource->id order by rc.lft asc";
 
        $sections = DB::select(DB::raw($raw));

        return view('lms.bonus.single-resource', ['resource' => $resource, 'sections'=>$sections]);
    }

} //class
