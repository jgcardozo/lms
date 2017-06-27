<?php

namespace App\Http\ViewComposers;

use Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class PaymentAlertsComposer
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

		// Figure out what course the user is watching
		if(!empty($view->course))
		{
			$course = $view->course;
		}else if(!empty($view->module))
		{
			$course = $view->module->course;
		}else if($view->lesson)
		{
			$course = $view->lesson->course;
		}

		$payf_session_key = 'payf_' . Auth::user()->id;

		$yesterday = Carbon::yesterday();
		$yesterday->hour = 23;
		$yesterday->minute = 59;
		$yesterday->second = 59;

		$today = Carbon::today();
		$today->hour = 23;
		$today->minute = 59;
		$today->second = 59;
		
		$payf_last_show = session($payf_session_key, $yesterday);

		if(!is_null($course) && Auth::user()->hasTag($course->payf_tag) && $payf_last_show->isPast())
		{
			$tagApplied = Auth::user()->is_tags->where('id', $course->payf_tag)->first()->pivot->created_at;
			$tagApplied = $tagApplied->diff($today)->days;

			$tagApplied = 7 - $tagApplied;
			if($tagApplied < 0)
			{
				$tagApplied = 0;
			}

			$alert['status'] = 'critical';
			$alert['message'] = '<strong>There was an issue with your last payment</strong>. Please <a href="' . route('user.billing') . '">CLICK HERE</a> to review & update your payment details. <br/> Your account will remain active for the next <strong>' . $tagApplied . ' days</strong>. You may also <a href="#" class="js-contact-customer-service">contact customer service</a> and we will be happy to help.';
			$alert['key'] = $payf_session_key;
		}

		$_askAlert = $view->getData();
		if(!empty($_askAlert['askAlert']))
		{
			$_askAlert = $_askAlert['askAlert'];
		}else{
			$_askAlert = [];
		}

		if(!is_null($alert))
		{
			$_askAlert[] = $alert;
		}

		$view->with('askAlert', $_askAlert);
	}
}