
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// const app = new Vue({
//     el: '#app'
// });

$(document).ready( function() {
	/**
	 * User menu
	 */
	$('body').on('click', '.dropdown-toggle', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.masthead__notifications-outer-wrap, .masthead__classes-wrap').stop().slideUp(200);
		$(this).next('.dropdown-menu-wrap').stop().slideToggle(200);
	});

	/**
	 * Classes menu
	 */
	$('body').on('click', '.js-header-classes', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.dropdown-menu-wrap, .masthead__notifications-outer-wrap').stop().slideUp(200);
		$(this).next('.masthead__classes-wrap').stop().slideToggle(200);
	});

	/**
	 * Notifications List Header
	 */
	$('body').on('click', '.js-header-notifications', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.dropdown-menu-wrap, .masthead__classes-wrap').stop().slideUp(200);
		$(this).next('.masthead__notifications-outer-wrap').stop().slideToggle(200);
	});

	$('.masthead__notifications-wrap').perfectScrollbar();	

	/**
	 * Close menus, dropdowns, etc. on body click 
	 */
	$('body').on('click', function() {
		$('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .masthead__classes-wrap').stop().slideUp(200);
	});

	/**
	 * Session Popup
	 */
	$('body').on('click', '.js-open-session', function(e) {
		e.preventDefault();

		$('.session-single').fadeIn();
	});

	$('body').on('click', '.session-single__close', function(e) {
		e.preventDefault();

		$('.session-single').fadeOut();
	});
});
