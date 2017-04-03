
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
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

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
	 * Session Popup handlers
	 */
	/**
	 * Wistia on second change event handler
	 *
	 * @param s
	 * @param video
     */
    function handleVideoSeconds(s, video) {
		var activeVideo = getVideoDetails();

		if(!activeVideo)
			return false;

		var complete_btn = activeVideo.popupWrap.find('.js-complete-session');


        // Show mark as complete buttons
        var totalProgress = activeVideo.progress + activeVideo.initProgress;
		if(totalProgress >= 80 && !complete_btn.is(':visible')) {
			$('body').find('#session-' + activeVideo.session + ' .course-progress').show();
			complete_btn.show();
		}

		if(activeVideo.currentProgress < activeVideo.progress)
            $('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress - activeVideo.currentProgress]);
    }

	/**
	 * Get video details only if there is a video popup opened
	 *
	 * @returns object Wistia Video and session id
     */
	function getVideoDetails() {
		var popupWrap = $('body').find('.session-single');

		if(!popupWrap.is(':visible'))
			return false;

		var videoWrap = popupWrap.find('.session-single__video'),
			session_id = videoWrap.data('session');
			video = Wistia.api(videoWrap.find('.wistia_embed').attr('id'));

		if(typeof session_id == 'undefined' || video == null)
			return false;

		var _progress = parseInt((video.secondsWatched() / video.duration()) * 100),
            updated_progress = videoWrap.data('updated');

        return {
			popupWrap: popupWrap,                       // Popup HTML wrap
			video: video,                               // Wistia Video Object
			session: session_id,                        // Session ID
			initProgress: videoWrap.data('progress'),   // Progress of the Wistia video, stored in the session when the video is played
			progress: _progress,                        // Progress of the Wistia video since it is started
            currentProgress: updated_progress           // Progress that is updated in PHP session from the time the video is played
		};
	}

	$('body').on('click', '.js-open-session', function(e) {
		e.preventDefault();

		var $this = $(this),
			url = this.href;

		$.ajax({
			url: url
		}).always(function(res) {
			$('.session-single__content-ajax').html(res);

            var videoWrap = $('body').find('.session-single .session-single__video'),
                wistiaId = videoWrap.data('video');

            videoWrap.data('updated', 0);

            window._wq = [];

            _wq.push({ id: wistiaId, onReady: function(video) {
				video.unbind('secondchange');
				video.unbind('pause');

                video.bind('secondchange', function(s) {
                    handleVideoSeconds(s, video);
                });

                video.bind('pause', function() {
					var activeVideo = getVideoDetails();

					if(!activeVideo)
						return false;

                    if(activeVideo.currentProgress < activeVideo.progress)
                        $('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress - activeVideo.currentProgress]);
                });
            }});

			$('.session-single').fadeIn();
		});
	});

	$('body').on('click', '.session-single__close', function(e) {
		e.preventDefault();

		var activeVideo = getVideoDetails();

		if(activeVideo)
			activeVideo.video.pause();

		$('.session-single').fadeOut();
	});

    window.addEventListener('beforeunload', function (e) {
		var activeVideo = getVideoDetails();

		if(activeVideo)
			$('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress]);
    });

    $('body').on('session.watch.stop', function(e, session_id, video, progress) {
        var activeVideo = getVideoDetails(),
            videoWrap = activeVideo.popupWrap.find('.session-single__video');

		var ajaxData = {
            id: session_id,
			progress: progress
		};

		$.ajax({
			type: 'POST',
			url: videoWrap.data('route'),
			data: ajaxData,
			success: function(res) {
				if(!activeVideo)
					return;

                var updated = videoWrap.data('updated');

				videoWrap.data('updated', updated + progress);
			}
		});
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

	/**
	 * Count textarea characters remaining
	 */
	$('.js-count-chars').on('keyup', function() {
		var $this = $(this),
			text_max = $this.attr('maxlength'),
        	text_length = $this.val().length,
        	text_remaining = text_max - text_length,
        	charsShow = $this.data('chars');

        $('.chars-count[data-chars="' + charsShow + '"] span').html(text_remaining);
    });

	$('body').on('click', '.course-progress', function() {
        var $this = $(this),
            completeHtml = '<div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>';

        $.ajax({
            url: $this.data('complete')
        }).always(function(res) {
            $this.replaceWith(completeHtml);
        });
    });

	/**
	 * Survey popup
	 */
	$('body').on('click', '.survey-popup__navigation a', function(e) {
        e.preventDefault();

		var $this = $(this),
			surPopupWrap = $this.closest('.survey-popup'),
			form = surPopupWrap.find('form'),
			currentStep = surPopupWrap.find('.survey-popup__step:visible'),
            nextBtn = form.find('.survey-popup__navigation__next'),
            prevBtn = form.find('.survey-popup__navigation__prev'),
            subBtn = form.find('.survey-popup__navigation__submit'),
            stepIndex = form.find('.survey-popup__step').index(currentStep);
        
        if($this.is('.next'))
        {
            var stepToShow = currentStep.next('.survey-popup__step');

            if(!form.parsley().validate({group: 'block-' + stepIndex}))
                return false;
        }else{
            var stepToShow = currentStep.prev('.survey-popup__step');
        }

        if(stepToShow.is(form.find('.survey-popup__step').first())) {
            nextBtn.show();
            prevBtn.hide();
            subBtn.hide();
        }else if(stepToShow.is(form.find('.survey-popup__step').last())) {
            nextBtn.hide();
            prevBtn.show();
            subBtn.show();
        }else{
            nextBtn.show();
            prevBtn.show();
            subBtn.hide();
        }

		currentStep.fadeOut(250, function() {
            stepToShow.fadeIn(250);
		});
	});

    $('body').on('click', '.js-open-surveyPopup', function() {
        var popup = $('body').find('.survey-popup');

        if(popup.length)
            popup.css('display', 'flex').hide().fadeIn(250);

        return false;
    });
});
