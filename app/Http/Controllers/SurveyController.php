<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');
	}

	public function testSurvey()
	{
		return view('lms.surveytest');
	}

	public function storeSurvey(Request $request)
	{
		$items = [];

		if(($request->has('q') && is_array($request->get('q'))) && ($request->has('a') && is_array($request->get('a'))))
		{
			$q_items = $request->get('q');
			$a_items = $request->get('a');

			foreach($q_items as $index => $value)
			{
				$items[] = [
					'q' => $value,
					'a' => $a_items[$index]
				];
			}

			if(!empty($items))
			{
				DB::table('surveys')->insert([
					'items' => json_encode($items),
					'user_id' => Auth::user()->id,
					'name' => $request->get('name'),
					'email' => $request->get('email'),
					'phone' => $request->get('phone')
				]);
			}
		}

		return redirect()->back();
	}

	public function deleteSurvey($id)
	{
		DB::table('surveys')->where('id', $id)->delete();

		return response()->json(['status' => true]);
	}

	public function table()
	{
		$data = DB::table('surveys')->get()->toArray();
		$data = array_map(function($el) {
			$el->items = json_decode($el->items);
			return $el;
		}, $data);

		return view('lms.admin.survey')->with('surveyData', $data);
	}
}