<?php
use Request;

/**
 * Chnage Header elements when on routes for Course, Module, Lesson
 */
if (!function_exists('changeHeader')) {
	function changeHeader() {
		if (Request::is('course/*') || Request::is('module/*') || Request::is('lesson/*')) { 
		  return true;
		}

		return false;
	}
}

/**
 * Check if it is home page / front page
 */
if (!function_exists('is_home')) {
	function is_home() {
		if (Request::is('/')) { 
		  return true;
		}

		return false;
	}
}