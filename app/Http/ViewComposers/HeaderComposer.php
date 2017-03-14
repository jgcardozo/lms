<?php

namespace App\Http\ViewComposers;

use Auth;
use App\Models\Course;
use Illuminate\View\View;

class HeaderComposer
{
	/**
	 * Bind data to the view.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$notifications = [];
		if($user = Auth::user())
		{
			$notifications['general'] = $user->unreadNotifications->where('type', 'App\Notifications\UnlockedByTag');
			$notifications['gamification'] = $user->unreadNotifications->where('type', 'App\Notifications\Gamification');
		}

		// Figure out what course the user is watching
		if(!empty($view->course))
		{
			$lms_items = $view->course;
		}else if(!empty($view->module))
		{
			$lms_items = $view->module->course()->with('modules.lessons.sessions')->first();
		}else if($view->lesson)
		{
			$lms_items = $view->lesson->course()->with('modules.lessons.sessions')->first();
		}

		$view->with('notifications', $notifications)->with('progress_items', $lms_items);
	}
}