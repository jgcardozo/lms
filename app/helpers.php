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