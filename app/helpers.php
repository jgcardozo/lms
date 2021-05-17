<?php
/**
 * Change Header elements when on routes for Course, Module, Lesson
 *
 * @return boolean
 */
if (!function_exists('changeHeader')) {
    function changeHeader()
    {
        if (Request::is('course/*') || Request::is('module/*') || Request::is('lesson/*')) {
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
if (!function_exists('is_home')) {
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
 *
 * @return string
 */
if (!function_exists('truncate_string')) {
    function truncate_string($string, $length = 20, $append = '&hellip;')
    {
        $string = strip_tags(trim($string));
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
        $words = explode(' ', $string);
        if (count($words) < $length) {
            $append = '';
        }

        return implode(' ', array_splice($words, 0, $length)) . $append;
    }
}

/**
 * Make first word in string bold / strong
 *
 * @param string $string
 *
 * @return string
 */
if (!function_exists('bold_first_word')) {
    function bold_first_word($string)
    {
        $title = preg_split("/\s+/", $string);
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
 *
 * @return string
 */
if (!function_exists('set_active_link')) {
    function set_active_link($path, $attr = false)
    {
        if ($attr) {
            return Request::is($path . '*') ? ' class=active' : '';
        }

        return Request::is($path . '*') ? ' active' : '';
    }
}

/**
 * Make bytes human readable
 *
 * @param float $bytes
 * @param int   $decimals
 *
 * @return string
 */
if (!function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

/**
 * Determinate if the current user is Administrator or Editor, same thing
 *
 * @return boolean
 */
if (!function_exists('is_role_admin')) {
    function is_role_admin()
    {
        return \Auth::user()
                    ->hasRole(['Administrator', 'Editor']);
    }
}

/**
 * Determinate if the current user is Vip
 *
 * @return boolean
 */
if (!function_exists('is_role_vip')) {
    function is_role_vip()
    {
        return \Auth::user()
                    ->hasRole(['Vip']);
    }
}

/**
 * Get setting via key
 */
if (!function_exists('lms_get_setting')) {
    function lms_get_setting($key, $default = null)
    {
        $row = \DB::table('settings')
                  ->where('key', $key)
                  ->pluck('value')
                  ->first();

        if (empty($row)) {
            return !empty($default) ? $default : false;
        }

        return $row;
    }
}

/**
 * Check ask-masterclass survey
 */
if (!function_exists('survey_check')) {
    function survey_check(\App\Models\Course $course)
    {
        // Popup before course start
        $popupCheck = DB::table('surveys')
                        ->where('user_id', \Auth::user()->id)
                        ->get()
                        ->toArray();
        if ($course->slug == 'ask-masterclass' && empty($popupCheck)) {
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
if (!function_exists('timezoneList')) {
    function timezoneList()
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
        $utcTime = new DateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = [];
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $_timezoneIdentifier = explode('/', $timezoneIdentifier);

            $tempTimezones[] = [
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'key' => $timezoneIdentifier,
                'identifier' => isset($_timezoneIdentifier[1]) ? $_timezoneIdentifier[1] : $_timezoneIdentifier[0]
            ];
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] == $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = [];
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
 * @param int $user_id   User ID
 * @param int $course_id Course ID
 * @param int $cc_id     Infusionsoft CreditCard IS
 */
if (!function_exists('addISCreditCard')) {
    function addISCreditCard($user_id, $course_id, $cc_id)
    {
        $qb = DB::table('payment_card_user')
                ->where('course_id', $course_id)
                ->where('user_id', $user_id);
        $check = $qb->get();
        if (!$qb->get()
                  ->isEmpty()
        ) {
            $qb->limit(1)
               ->update(['cc_id' => $cc_id]);
        } else {
            DB::table('payment_card_user')
              ->insert([
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
if (!function_exists('mixPanel')) {
    function mixPanel()
    {
        $mp = Mixpanel::getInstance(env('MIXPANEL_TOKEN'));

        /**
         * Assume that only logged in users can use the LMS
         */
        if (\Auth::check()) {
            $user = \Auth::user();

            $mp->people->set($user->id, [
                '$first_name' => $user->profile && $user->profile->first_name ? $user->profile->first_name : '',
                '$last_name' => $user->profile && $user->profile->last_name ? $user->profile->last_name : '',
                '$email' => $user->email ?: ''
            ]);

            $mp->identify($user->id);
        }

        return $mp;
    }
}

/**
 * Get basic LMS stats.
 */
if (!function_exists('getBasicLessonsStats')) {
    function getBasicLessonsStats()
    {
        // 2880 Minutes - 2 days
        return \Cache::remember('lms.stats', 2880, function () {
            $totalUsers = \App\Models\User::count();

            $lessons = \App\Models\Lesson::select(['id'])
                                         ->with('sessions:id,lesson_id')
                                         ->get();

            $result = [];
            foreach ($lessons as $lesson) {
                $sessionIds = $lesson->sessions->pluck('id')
                                               ->toArray();

                $rawQuery = \DB::table('session_user')
                               ->whereIn('session_id', $sessionIds)
                               ->get();

                $results = $rawQuery->groupBy('user_id')
                                    ->filter(function ($item) use ($sessionIds) {
                                        return count($item) == count($sessionIds);
                                    });

                $result[$lesson->id]['finished'] = count($results);
                $result[$lesson->id]['unfinished'] = $totalUsers - count($results);
                $result[$lesson->id]['total'] = $result[$lesson->id]['finished'] + $result[$lesson->id]['unfinished'];
                $result[$lesson->id]['percent'] = round(($result[$lesson->id]['finished'] / $result[$lesson->id]['total']) * 100, 2);
            }

            return $result;
        });
    }
}

/**
 * Compile shortcodes
 */
if (!function_exists('compileShortcodes')) {
    function compileShortcodes($str)
    {
        $str = htmlspecialchars_decode($str);

        preg_match('/\[button(.*?)?\](?:(.+?)?\[\/button\])?/', $str, $matches);
        if (!empty($matches)) {
            $button_text = trim($matches[2]);
            $attributes = trim($matches[1]);
            preg_match_all('/(\w+)=["\'](.*?)["\']/', $attributes, $attributes_matches);

            $attributes_keys = $attributes_matches[1];
            $attributes_values = $attributes_matches[2];

            $urlKey = array_search('url', $attributes_keys);
            if ($urlKey === false) {
                return str_replace($matches[0], '', $str);
            }

            $url = $attributes_values[$urlKey];
            if (!empty($url)) {
                $button = '<a class="button" href="' . $url . '">' . $button_text . '</a>';
            }

            if (!empty($button)) {
                return str_replace($matches[0], $button, $str);
            }

            return str_replace($matches[0], '', $str);
        }

        return $str;
    }
}

/**
 * Compile shortcodes
 */
if (!function_exists('rollbar_get_current_user')) {
    function rollbar_get_current_user()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return [
                    'id' => $user->id,
                    'username' => $user->email,
                    'email' => $user->email
                ];
        }

        return null;
    }
}

/**
 * LMS v2 url with ID and Remember Token
 */
if (!function_exists('link_to_lms2')) {
    function link_to_lms2()
    {
        $url = env('LMSV2_URL', 'https://ask.academy');
        if (!auth()->check()) {
            return $url;
        }

        $user = auth()->user();
        $hash = encrypt($user->id);
        
        return $url."?hash={$hash}&token={$user->remember_token}";
    }
}

/**
 * LMS v2 url with ID and Remember Token
 */
if (!function_exists('form_to_lms2')) {
    function form_to_lms2($image=null, $course=null)
    {
        try {
            $url = env('LMSV2_URL', 'https://ask.academy');
            if (!auth()->check()) {
                return $url;
            }

            $user = auth()->user();
            $hash = encrypt($user->id);

            $form = '<form action="'.$url.'" method="post">';
            $form .= '<input type="hidden" name="hash" value="'.$hash.'">';
            $form .= '<input type="hidden" name="token" value="'.$user->remember_token.'">';
            if ($image) {
                $form .= '<button type="submit" class="masthead__classes-link" ';
                if ($course->logo_image) {
                    $form .= 'style="border: none; border-bottom: 0.1rem solid #dadada; padding-top: 10px; padding-bottom: 10px; background-image: url('.$course->getLogoImageUrlAttribute().'); width: 100%; text-align: left;"';
                } else {
                    $form .= 'style="border: none"';
                }
                $form .= '>'.bold_first_word($course->title).'</button>';
            } else {
                $form .= '<button type="submit" class="courseblock__link">Access Training</button>';
            }
            $form .= csrf_field();
            $form .= '</form>';
            
            return $form;
        } catch (Exception $e) {
            return '';
        }
    }
}
