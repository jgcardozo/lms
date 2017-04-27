<?php

namespace App\Http\ViewComposers;

use Auth;
use Illuminate\View\View;

class NotificationsComposer
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
			$notifications['general'] = $user->unreadNotifications->where('type', '!=', 'App\Notifications\UnlockedByTag');
			$notifications['gamification'] = $user->unreadNotifications->where('type', 'App\Notifications\Gamification');
		}

		$view->with('notifications', $notifications);
	}
}