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
        $allResourcesBank = ResourcesBank::orderBy('lft', 'ASC')->get();
        $allBonuses = Bonus::orderBy('lft', 'ASC')->get();
        $bonuses = $resources = [];

        foreach ($allResourcesBank as $resource) {
            if ($resource->published) {
                $resources[] = $resource;
            }
        }

        foreach ($allBonuses as $bonus) {
            if (!$bonus->is_locked) {
                $bonuses[] = $bonus;
            }
        }
        return view('lms.bonus.index', ['bonuses' => $bonuses, 'resources' => $resources]);
    }

    public function show($slug)
    {
        $bonus = Bonus::whereSlug($slug)
            ->first();

        return view('lms.bonus.single', ['bonus' => $bonus]);
    }
}
