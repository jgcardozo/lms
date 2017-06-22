<?php

namespace App\Http\ViewComposers;

use Auth;
use Illuminate\View\View;

class LessonCongratulation
{
	/**
	 * Bind data to the view.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$viewData = $view->getData();
		if(!empty($viewData['lesson']))
		{
			$lesson = $viewData['lesson'];

			if(!empty($lesson->q_answered) && !$lesson->test_finished)
			{
				$view->with('lessonCongratulation', true);
			}
		}
	}
}