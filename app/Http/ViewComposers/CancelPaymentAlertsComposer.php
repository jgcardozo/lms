<?php

namespace App\Http\ViewComposers;

use Auth;
use Carbon\Carbon;
use App\Models\Course;
use Illuminate\View\View;

class CancelPaymentAlertsComposer
{
	/**
	 * Bind data to the view.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$course = null;
		$alert = null;

		$cancel_session_key = 'cancel_' . Auth::user()->id;

		$yesterday = Carbon::yesterday();
		$yesterday->hour = 23;
		$yesterday->minute = 59;
		$yesterday->second = 59;

		$today = Carbon::today();
		$today->hour = 23;
		$today->minute = 59;
		$today->second = 59;

		$cancel_last_show = session($cancel_session_key, $yesterday);

		$courses = Course::get();
		foreach($courses as $course)
		{
			if(Auth::user()->hasTag($course->cancel_tag) && $cancel_last_show->isPast() && is_null($alert))
			{
				$alert['status'] = 'critical';
				$alert['message'] = '<strong>Your account is suspended :(</strong>. We\'ve tried charging your credit card multiple times without success. To continue your <strong>' . $course->title . '</strong> education please update your payment information. Thank you';
				$alert['key'] = $cancel_session_key;
			}
		}

		$_askAlert = $view->getData();
		if(!empty($_askAlert['askAlert']))
		{
			$_askAlert = $_askAlert['askAlert'];
		}else{
			$_askAlert[] = [];
		}

		if(!is_null($alert))
		{
			$_askAlert[] = $alert;
		}

		$view->with('askAlert', $_askAlert);
	}
}