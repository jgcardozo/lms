<?php
/**
 * Change Header elements when on routes for Course, Module, Lesson
 */
if ( !function_exists('changeHeader') ) {
	function changeHeader() {
		if ( Request::is('course/*') || Request::is('module/*') || Request::is('lesson/*') ) { 
		  return true;
		}

		return false;
	}
}

/**
 * Check if it is home page / front page
 */
if ( !function_exists('is_home') ) {
	function is_home() {
		if (Request::is('/')) { 
		  return true;
		}

		return false;
	}
}

/**
 * Truncate string to number of characters
 */
if ( !function_exists('truncate_string') ) {
	function truncate_string( $string, $length=100, $append="&hellip;" ) {
	  $string = strip_tags( trim( $string ) );

	  if( strlen($string) > $length ) {
	    $string = wordwrap( $string, $length );
	    $string = explode( "\n", $string, 2 );
	    $string = $string[0] . $append;
	  }

	  return $string;
	}
}

/**
 * Make first word in string bold / strong
 */
if ( !function_exists('bold_first_word') ) {
	function bold_first_word( $string ) {
		$title = preg_split("/\s+/",  $string);
		$title[0] = "<strong> $title[0] </strong>";
		$title = join(' ', $title);

		return $title;
	}
}