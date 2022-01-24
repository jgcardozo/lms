<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Course;
use Illuminate\Http\Request;

class BonusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allBonuses = Bonus::orderBy('lft', 'ASC')->get();
        $bonuses = [];

        foreach($allBonuses as $bonus)
        {
            if(!$bonus->is_locked)
            {
                $bonuses[] = $bonus;
            }
        }

        return view('lms.bonus.index', ['bonuses' => $bonuses]);
    }

    public function show($slug)
    {
        $bonus = Bonus::whereSlug($slug)
                      ->first();

        return view('lms.bonus.single', ['bonus' => $bonus]);
    }
}
