
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

		$('.masthead__notifications-outer-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
		$(this).next('.dropdown-menu-wrap').stop().slideToggle(200);
	});

	/**
	 * Classes menu
	 */
	$('body').on('click', '.js-header-classes', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .course-progress-box').stop().slideUp(200);
		$(this).next('.masthead__classes-wrap').stop().slideToggle(200);
	});

	/**
	 * Notifications List Header
	 */
	$('body').on('click', '.js-header-notifications', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.dropdown-menu-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
		$(this).next('.masthead__notifications-outer-wrap').stop().slideToggle(200);
	});

	$('.masthead__notifications-wrap').perfectScrollbar();	

	/**
	 * Close menus, dropdowns, etc. on body click 
	 */
	$('body').on('click', function() {
		$('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
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

	/**
	 * Course Progress
	 */
	$('body').on('click', '.js-header-progress', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .masthead__classes-wrap').stop().slideUp(200);
		$('.course-progress-box').stop().slideToggle(200);
	});

	$('body').on('click', '.js-header-close-progress', function(e) {
		e.stopPropagation();
		e.preventDefault();

		$('.course-progress-box').stop().slideUp(200);
	});

	$('body').on('click', '.course-progress-box', function(e) {
		e.stopPropagation();
	});

	var timerToClose;
	$('.course-progress-box__item--lesson-mark').on({
		mouseenter: function() {
			clearTimeout(timerToClose);

			$('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
			$(this).addClass('is-hover');
			$(this).next('.course-progress-box__item--lesson-mark__info').addClass('is-hover');
		},
		mouseleave: function() {
			timerToClose = window.setTimeout( function() {
				$('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
			}, 200);
		}
	});

	$('.course-progress-box__item--lesson-mark__info').on({
		click: function(e) {
			e.stopPropagation();
		},
		mouseenter: function() {
			clearTimeout(timerToClose);
			
			$('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
			$(this).addClass('is-hover');
			$(this).prev('.course-progress-box__item--lesson-mark').addClass('is-hover');
		},
		mouseleave: function() {
			timerToClose = window.setTimeout( function() {
				$('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
			}, 200);
		}
	});

	/**
	 * Events Calendar
	 */
	$( "#datepicker" ).datepicker({
      numberOfMonths: 2,
      dayNamesMin: ["S", "M", "T", "W", "T", "F", "S"]
    });

    /**
     * Circular Progress
     */
    
    $(window).on('load', function() {
	    $('.course-progress__bar--active').each(function() {
	    	var percent = $(this).data("percentage");

	    	var circle = new ProgressBar.Circle($(this)[0], {
			    color: '#62D262',
			    strokeWidth: 18.75
			});

			circle.animate(percent);  // Number from 0.0 to 1.0
	    });

	    $('.module__active').each(function() {
	    	var percent = $(this).data("percentage");

	    	var circle = new ProgressBar.Circle($(this)[0], {
			    color: '#62D262',
			    strokeWidth: 18.75
			});

			circle.animate(percent);  // Number from 0.0 to 1.0
	    });
	});	
});
