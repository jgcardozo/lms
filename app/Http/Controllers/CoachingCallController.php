<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoachingCall;
use App\Scopes\CoachingCallUserScope;
use Illuminate\Http\Request;
use App\Scopes\SessionTypeScope;

class CoachingCallController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $course = Course::findBySlugOrFail($slug);
        $coaching_calls = $course->coachingcall;

        $coaching_calls = $coaching_calls->filter(function ($coaching_call) {
            if(is_role_admin() || is_role_vip()) {
                return true;
            }

            if($coaching_call->cohorts->count()) {
                $userCohorts = auth()->user()->cohorts->pluck('id');
                $coachingCallCohorts = $coaching_call->cohorts->pluck('id');
                if($coachingCallCohorts->intersect($userCohorts)->isEmpty()) {
                    return false;
                }
            }

            return true;
        });

        /** @var Course $course */
        $top_coaching_calls = $course->coachingcall()
                                     ->withoutGlobalScopes()
                                     ->get();

        $top_coaching_calls = $top_coaching_calls->where('top_coachingcall', '=', 1);

        return view('lms.coachingcalls.single')
            ->with(['coaching_calls' => $coaching_calls])
            ->with(['top_coaching_calls' => $top_coaching_calls])
            ->with(['course' => $course]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CoachingCall $coachingCall
     *
     * @return \Illuminate\Http\Response
     */
    public function show($course, $id)
    {
        $coaching_call = CoachingCall::withoutGlobalScope(CoachingCallUserScope::class)->findOrFail($id);

        return view('lms.coachingcalls.session-popup')->with('coaching_call', $coaching_call);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CoachingCall $coachingCall
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CoachingCall $coachingCall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\CoachingCall $coachingCall
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoachingCall $coachingCall)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CoachingCall $coachingCall
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoachingCall $coachingCall)
    {
        //
    }
}
