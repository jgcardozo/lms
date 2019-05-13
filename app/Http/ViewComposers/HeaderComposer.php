<?php

namespace App\Http\ViewComposers;

use Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Bonus;
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
		$lms_items = null;
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

		if(!empty($lms_items))
		{
			$courses = Course::where('id', '!=', $lms_items->id)->get();
		}else{
			$courses = Course::get();
		}

		$view->with('notifications', $notifications)->with('progress_items', $lms_items)->with('courses', $courses)->with('bonuses', $this->getBonuses($user));
	}

	private function getBonuses($user)
    {
        if(!$user)
        {
            return [];
        }

        $userTags = $user->is_tags->pluck('id')->toArray();

        return Bonus::whereHas('lock_tags', function($query) use($userTags) {
            $query->whereIn('is_lockables.tag_id', $userTags);
        })->get();
    }
}