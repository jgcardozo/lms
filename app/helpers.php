<?php
/**
 * Change Header elements when on routes for Course, Module, Lesson
 *
 * @return boolean
 */
if ( !function_exists('changeHeader') ) {
	function changeHeader()
	{
		if ( Request::is('course/*') || Request::is('module/*') || Request::is('lesson/*') ) { 
		  return true;
		}

		return false;
	}
}

/**
 * Check if it is home page / front page
 *
 * @return boolean
 */
if ( !function_exists('is_home') ) {
	function is_home()
	{
		if (Request::is('/')) { 
		  return true;
		}

		return false;
	}
}

/**
 * Truncate string to number of characters
 *
 * @param  string  $string
 * @param  integer $length [length of returned string]
 * @param  string  $append [string to append at the end of the returned string]
 * @return string
 */
if ( !function_exists('truncate_string') ) {
	function truncate_string( $string, $length=20, $append="&hellip;" )
	{
        $string = strip_tags( trim( $string ) );
        /*
        if( strlen($string) > $length ) {
            $string = wordwrap( $string, $length );
            $string = explode( "\n", $string, 2 );
            $string = $string[0] . $append;
        }

        return $string;
        */

        /*$text = '';
        if (str_word_count($string, 0) > $length) {
            $words = str_word_count($string, 2);
            $pos = array_keys($words);
            $text = substr($string, 0, $pos[$length]) . $append;
        }

        return $text;
        */
        $words = explode(" ",$string);
		if(count($words) < $length)
		{
			$append = '';
		}

        return implode(" ",array_splice($words, 0, $length)) . $append;
	}
}

/**
 * Make first word in string bold / strong
 *
 * @param string $string
 * @return string
 */
if ( !function_exists('bold_first_word') ) {
	function bold_first_word( $string )
	{
		$title = preg_split("/\s+/",  $string);
		$title[0] = "<strong> $title[0] </strong>";
		$title = join(' ', $title);

		return $title;
	}
}

/**
 * Set active class to current path
 * 
 * @param string  $path 
 * @param boolean $attr [Set active class together with attribute class]
 * @return string
 */
if ( !function_exists('set_active_link') ) {	
	function set_active_link( $path, $attr=false )
	{
		if ( $attr ) {
			return Request::is($path . '*') ? ' class=active' :  '';	
		}

		return Request::is($path . '*') ? ' active' :  '';
	}
}

/**
 * Make bytes human readable
 *
 * @param float $bytes
 * @param int   $decimals
 * @return string
 */
if ( !function_exists('human_filesize') ) {
	function human_filesize($bytes, $decimals = 2)
	{
		$size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
}

/**
 * Determinate if the current user is Administrator or Editor, same thing
 *
 * @return boolean
 */
if ( !function_exists('is_role_admin') ) {
	function is_role_admin()
	{
		return \Auth::user()->hasRole(['Administrator','Editor']);
	}
}

/**
 * Determinate if the current user is Vip
 *
 * @return boolean
 */
if ( !function_exists('is_role_vip') ) {
	function is_role_vip()
	{
		return \Auth::user()->hasRole(['Vip']);
	}
}

/**
 * Get setting via key
 */
if ( !function_exists('lms_get_setting') ) {
	function lms_get_setting($key, $default = null)
	{
		$row = \DB::table('settings')->where('key', $key)->pluck('value')->first();

		if(empty($row))
		{
			return !empty($default) ? $default : false;
		}

		return $row;
	}
}

/**
 * Check ask-masterclass survey
 */
if ( !function_exists('survey_check') ) {
	function survey_check(\App\Models\Course $course)
	{
		// Popup before course start
		$popupCheck = DB::table('surveys')->where('user_id', \Auth::user()->id)->get()->toArray();
		if($course->slug == 'ask-masterclass' && empty($popupCheck)) {
			return true;
		}

		return false;
	}
}

/**
 * Return array of timezones
 *
 * @return array
 */
if ( !function_exists('timezoneList') ) {
	function timezoneList()
	{
		$timezoneIdentifiers = DateTimeZone::listIdentifiers();
		$utcTime = new DateTime('now', new DateTimeZone('UTC'));

		$tempTimezones = array();
		foreach ($timezoneIdentifiers as $timezoneIdentifier) {
			$currentTimezone = new DateTimeZone($timezoneIdentifier);

			$_timezoneIdentifier = explode('/', $timezoneIdentifier);

			$tempTimezones[] = array(
				'offset' => (int)$currentTimezone->getOffset($utcTime),
				'key' => $timezoneIdentifier,
				'identifier' => isset($_timezoneIdentifier[1]) ? $_timezoneIdentifier[1] : $_timezoneIdentifier[0]
			);
		}

		// Sort the array by offset,identifier ascending
		usort($tempTimezones, function($a, $b) {
			return ($a['offset'] == $b['offset'])
				? strcmp($a['identifier'], $b['identifier'])
				: $a['offset'] - $b['offset'];
		});

		$timezoneList = array();
		foreach ($tempTimezones as $tz) {
			$sign = ($tz['offset'] > 0) ? '+' : '-';
			$offset = gmdate('H:i', abs($tz['offset']));
			$timezoneList[$tz['key']] = '(UTC ' . $sign . $offset . ') ' .
				$tz['identifier'];
		}

		return $timezoneList;
	}
}

/**
 * Adds user credit card to course
 *
 * @param int $user_id    User ID
 * @param int $course_id  Course ID
 * @param int $cc_id      Infusionsoft CreditCard IS
 */
if ( !function_exists('addISCreditCard') ) {
	function addISCreditCard($user_id, $course_id, $cc_id)
	{
		$qb = DB::table('payment_card_user')->where('course_id', $course_id)->where('user_id', $user_id);
		$check = $qb->get();
		if(!$qb->get()->isEmpty())
		{
			$qb->limit(1)->update(['cc_id' => $cc_id]);
		}else{
			DB::table('payment_card_user')->insert([
				[
					'user_id' => $user_id,
					'course_id' => $course_id,
					'cc_id' => $cc_id
				]
			]);
		}
	}
}

/**
 * Get Mixpanel instance. Easy way.
 */
if ( !function_exists('mixPanel') ) {
	function mixPanel()
	{
		$mp = Mixpanel::getInstance(env('MIXPANEL_TOKEN'));

		/**
		 * Assume that only logged in users can use the LMS
		 */
		if(\Auth::check())
		{
			$user = \Auth::user();

			$mp->people->set($user->id, array(
				'$first_name'       => $user->profile->first_name ?: '',
				'$last_name'        => $user->profile->last_name ?: '',
				'$email'            => $user->email ?: ''
			));

			$mp->identify($user->id);
		}

		return $mp;
	}
}

/**
 * Get basic LMS stats.
 */
if ( !function_exists('getBasicLessonsStats') ) {
	function getBasicLessonsStats()
	{
		$users = \App\Models\User::get();
		$lessons = \App\Models\Lesson::get();

		// 2880 Minutes - 2 days
		$lessonsFinished = \Cache::remember('lms.stats', 2880, function () use ($users, $lessons) {
			$_lessonsFinished = [];
			foreach($lessons as $lesson)
			{
				$_lessonsFinished[$lesson->id]['finished'] = 0;
				$_lessonsFinished[$lesson->id]['unfinished'] = 0;

				foreach($users as $user)
				{
					if(!$lesson->getIsCompletedAttribute($user->id))
					{
						$_lessonsFinished[$lesson->id]['unfinished'] = $_lessonsFinished[$lesson->id]['unfinished'] + 1;
						continue;
					}

					$_lessonsFinished[$lesson->id]['finished'] = $_lessonsFinished[$lesson->id]['finished'] + 1;
				}

				$_lessonsFinished[$lesson->id]['total'] = $_lessonsFinished[$lesson->id]['finished'] + $_lessonsFinished[$lesson->id]['unfinished'];
				$_lessonsFinished[$lesson->id]['percent'] = round(($_lessonsFinished[$lesson->id]['finished'] / $_lessonsFinished[$lesson->id]['total']) * 100, 2);
			}

			return $_lessonsFinished;
		});

		return $lessonsFinished;
	}
}