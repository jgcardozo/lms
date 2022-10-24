<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\ResourcesBank;

class BonusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allResourcesBank = ResourcesBank::orderBy('lft', 'ASC')->orderBy('created_at', 'DESC')->get();
        $allBonuses = Bonus::orderBy('lft', 'ASC')->orderBy('created_at', 'DESC')->get();
        $bonuses = $resources = $collection = [];

        $key=1;
        foreach ($allResourcesBank as $resource) {
            if (!$resource->is_locked) {
                $resource['type'] = 'resource';
                $resources[] = $resource;
                $collection[$key]=$resource;
            }
            $key=$key+2;
        } 
       
        $key=0;
        foreach ($allBonuses as $bonus) {
            if (!$bonus->is_locked) {
                $bonus['type'] = 'bonus';
                $bonuses[] = $bonus;
                $collection[$key] = $bonus;
            }
            $key = $key + 2;
        }
    
        ksort($collection);
        return view('lms.bonus.index', ['bonuses' => $bonuses, 'resources' => $resources, 'collection'=>$collection]);
    }



    public function show($slug)
    {
        $bonus = Bonus::whereSlug($slug)->first();
        if ($bonus->is_locked){
           return \Redirect::to('/');
        } 
        return view('lms.bonus.single', ['bonus' => $bonus]);
    }
}
