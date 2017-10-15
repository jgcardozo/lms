<?php

namespace App\Http\ViewComposers;

use Auth;
use Carbon\Carbon;
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
			$data = $user->notifications->where('type', '!=', 'App\Notifications\UnlockedByTag'); // All notifications

			$notifications = [
				'data' => $data,
                'not_displayed' => $data->where('display_at', null)->count() ? $data->where('display_at', null) : [],
				'count_unread' => $data->where('read_at', null)->count()
			];
		}

		$view->with('notifications', $notifications);
	}
}