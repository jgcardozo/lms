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
		$cancel_session_key = 'cancel_' . Auth::user()->id;

		$yesterday = Carbon::yesterday();
		$yesterday->hour = 23;
		$yesterday->minute = 59;
		$yesterday->second = 59;

		$today = Carbon::today();
		$today->hour = 23;
		$today->minute = 59;
		$today->second = 59;
		
		$payf_last_show = session($payf_session_key, $yesterday);
		$cancel_last_show = session($cancel_session_key, $yesterday);

		// $payf_last_show = $yesterday;

		if(!is_null($course) && Auth::user()->hasTag($course->payf_tag) && $payf_last_show->isPast())
		{
			$alert['status'] = 'critical';
			$alert['message'] = '<strong>We had a problem charging your credit card</strong>, please review & update your payment details. Your account will remain active for the next <strong>7 days.</strong>';
			$alert['key'] = $payf_session_key;
		}

		if(!is_null($course) && Auth::user()->hasTag($course->cancel_tag) && $cancel_last_show->isPast())
		{
			$alert['status'] = 'critical';
			$alert['message'] = '<strong>Your account is suspended :(</strong>. We\'ve tried charging your credit card multiple times without success. To continue your Ask education please update your payment information. Thank you';
			$alert['key'] = $cancel_session_key;
		}

		$view->with('askAlert', $alert);
	}
}