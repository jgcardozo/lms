<?php

namespace App\Http\Controllers;

use App\Models\ResourcesBank;
use Illuminate\Support\Facades\DB;

class ResourcesBankController extends Controller
{

    public function show($slug)
    {
        $resource = ResourcesBank::whereSlug($slug)->first();

        $raw = "select rr.container_id, rb.id, rr.section_id , rc.id, rc.title, rc.slug, rc.content  from rcontainer_rsection rr
        join resources_children rc on rc.id=rr.section_id
        join resources_banks rb on rb.id=rr.container_id
        where container_id=$resource->id";

        $sections = DB::select(DB::raw($raw));

        return view('lms.bonus.single-resource', ['resource' => $resource, 'sections'=>$sections]);
    }

} //class
