/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./jquery.payment.min');
window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('logs-table', require('./components/LogsTable.vue'));
Vue.component('spinner', require('./components/Spinner.vue'));

import { Datetime } from 'vue-datetime'
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css'
 
Vue.use(Datetime)

const app = new Vue({
    el: '#app'
});

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /**
     * Loading
     */
    $.fn.createLoading = function () {
        var template = $('#loading-template').html();

        this.each(function () {
            $(this).append(template);
        });
    }

    $.fn.removeLoading = function () {
        this.each(function () {
            $(this).find('div.loading').remove();
        });
    }

    /**
     * User menu
     */
    $('body').on('click', '.dropdown-toggle', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $('.masthead__notifications-outer-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
        $(this).next('.dropdown-menu-wrap').stop().slideToggle(200);
    });

    /**
     * Classes menu
     */
    $('body').on('click', '.js-header-classes', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .course-progress-box').stop().slideUp(200);
        $(this).next('.masthead__classes-wrap').stop().slideToggle(200);
    });

    /**
     * Notifications List Header
     */
    $('body').on('click', '.js-header-notifications', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $('.dropdown-menu-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
        $(this).next('.masthead__notifications-outer-wrap').stop().slideToggle(200);
    });

    $('.masthead__notifications-list').perfectScrollbar();

    $('body').on('click', '.js-notifications-mark-as-read', function (e) {
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this);

        $.ajax({
            type: 'POST',
            url: $this.data('route'),
            data: [],
            success: function (res) {
                $this.parent().next().find('.masthead__notifications-list__item--unread').removeClass('masthead__notifications-list__item--unread');
                $('body').find('.masthead__notifications__count').remove();
            }
        });
    });

    $(".masthead__notifications-list__item ").click(function () {
        $(".masthead__notifications-list__item > a").click();
    });

    $('body').on('click', '.js-close-popup-notification', function (e) {
        e.preventDefault();

        var $this = $(this);
        
        var id = $('#notificationId').text();

        console.log(id);
        
        $.ajax({
            type: 'POST',
            url: 'notifications/read',
            data: {
                "notificationId" : id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                var count = $(".masthead__notifications__count").text();
                if(count > 1) {
                    $(".masthead__notifications__count").text(count - 1)
                } else {
                    $(".masthead__notifications__count").remove();
                }
            }
        });

        $this.closest('.popup-notification').fadeOut(150, function () {
            $(this).remove();
        });
    });

    /**
     * Close menus, dropdowns, etc. on body click
     */
    $('body').on('click', function () {
        $('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .masthead__classes-wrap, .course-progress-box').stop().slideUp(200);
    });

    /**
     * Submit bonus questions on lesson
     */
    $('body').find('form.js-lesson-answer-question').on('submit', function (e) {
        e.preventDefault();

        var frm = $(this),
            ajaxData = frm.serialize();

        if (frm.is('.doing'))
            return false;

        frm.addClass('doing');
        $('body').createLoading();

        $.ajax({
            url: frm.attr('action'),
            data: ajaxData,
            type: 'POST',
        }).always(function (res) {
            if (res.status) {
                $('.session-single__content-ajax').html(res.popup);
                $('body').find('.session-single__close').addClass('question-close');
                $('body').css('overflow', 'hidden');
                $('.session-single').fadeIn(250);
            }

            frm.removeClass('doing');
            $('body').removeLoading();
        });
    });

    $('body').on('click', '.question-close', function (e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
        e.preventDefault();

        var el = $(this),
            popupWrap = el.parent().find('.session-single__content-ajax'),
            student = popupWrap.find('.session-single__student'),
            quiz = popupWrap.find('.js-assessment');

        if ($('body').find('.easteregg-step-2').is(':visible') || $('body').find('.easteregg-step-3').is(':visible')) {
            location = location.href;
        }

        $('body').find('.easteregg-step-1').hide(50);
        $('body').find('.easteregg-step-2').fadeIn(150);
    });

    $('body').on('click', '.js-assessment-link', function (e) {
        e.preventDefault();

        var el = $(this),
            wrap = el.closest('.session-single__content-ajax');

        el.parents('.easteregg-step-2').hide(50);
        $('body').find('.easteregg-step-3').fadeIn(150);

        checkForQuiz(el);
    });

    $('body').on('click', '.js-play-question-video', function (e) {
        e.preventDefault();

        var $this = $(this),
            url = $(this).attr('href');

        $.ajax({
            url: url
        }).always(function (res) {
            $('.session-single__content-ajax').html(res);

            $('body').find('.session-single__close');
            $('body').css('overflow', 'hidden');
            $('.session-single').fadeIn(250);
        });
    });

    $('body').on('click', '.js-retake-assessment', function (e) {
        e.preventDefault();

        var el = $(this);

        $('body').createLoading();

        $.ajax({
            url: el.data('popup'),
            type: 'POST',
        }).always(function (res) {
            if (res.status) {
                var wrap = $('.session-single__content-ajax');
                wrap.html(res.popup);
                wrap.find('> *').not('.js-assessment').hide();
                wrap.find('.js-assessment').show();
                checkForQuiz(el);
                $('body').css('overflow', 'hidden');
                $('.session-single').fadeIn(250);
            }

            $('body').removeLoading();
        });
    });

    function checkForQuiz(el) {
        var url = el.data('url'),
            user = el.data('user'),
            test = el.data('test'),
            href = el.attr('href'),
            taken = el.data('taken'),
            count = 1;

        if (typeof taken === 'undefined') {
            taken = '';
        }

        var ajaxData = {
            user_id: user,
            test_id: test,
            taken: taken
        };

        var x = setInterval(function () {
            /*
             if(count >= 5)
             {
             clearInterval(x);
             }
             */

            count++;

            $.ajax({
                type: 'POST',
                url: url,
                data: ajaxData,
                success: function (res) {
                    console.log(res);
                    if (res.status) {
                        if (res.first_time) {
                            dataLayer.push({
                                'event': 'exam',
                                'score': res.score
                             });
                        }
                        clearInterval(x);
                        window.location = href;
                    }
                }
            });
        }, 5000);

        $('body').find('.session-single__close').on('click', function (e) {
            e.preventDefault();
            clearInterval(x);
        });
    }

    /**
     * Session Popup handlers
     */

    /**
     * Get parameter from url query
     *
     * @param sParam
     * @returns {*}
     */
    function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    }

    /**
     * Wistia on second change event handler
     *
     * @param s
     * @param video
     */
    function handleVideoSeconds(s, video) {
        var activeVideo = getVideoDetails();

        if (!activeVideo)
            return false;

        var complete_btn = activeVideo.popupWrap.find('.js-complete-session');

        // Show mark as complete buttons
        var totalProgress = activeVideo.progress + activeVideo.initProgress;
        if (totalProgress >= 80 && !complete_btn.is(':visible')) {
            $('body').find('#session-' + activeVideo.session + ' .course-progress').show();
            complete_btn.show();
        }

        if (activeVideo.currentProgress < activeVideo.progress)
            $('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress - activeVideo.currentProgress]);
    }

    /**
     * Get video details only if there is a video popup opened
     *
     * @returns object Wistia Video and session id
     */
    function getVideoDetails() {
        var popupWrap = $('body').find('.session-single');

        if (!popupWrap.is(':visible'))
            return false;

        var videoWrap = popupWrap.find('.session-single__video');

        var isWistia = videoWrap.data('videotype');
        if(isWistia != 1)
        {
            return false;
        }

        if (videoWrap) {
            var session_id = videoWrap.data('session');
            var video = Wistia.api(videoWrap.find('.wistia_embed').attr('id'));

            if (typeof session_id == 'undefined' || video == null)
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
    }

    $('body').on('click', '.js-open-session', function (e) {
        e.preventDefault();

        var $this = $(this),
            url = $(this).data('href');

        $.ajax({
            url: url
        }).always(function (res) {
            $('.session-single__content-ajax').html(res);

            var videoWrap = $('body').find('.session-single .session-single__video');

            if (videoWrap) {
                var isWistia = videoWrap.data('videotype');
                if(isWistia == 1)
                {
                    var wistiaId = videoWrap.data('video');
                    videoWrap.data('updated', 0);

                    window._wq = [];

                    _wq.push({
                        id: wistiaId, onReady: function (video) {
                            video.unbind('secondchange');
                            video.unbind('pause');

                            video.bind('secondchange', function (s) {
                                handleVideoSeconds(s, video);
                            });

                            video.bind('pause', function () {
                                var activeVideo = getVideoDetails();

                                if (!activeVideo)
                                    return false;

                                if (activeVideo.currentProgress < activeVideo.progress)
                                    $('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress - activeVideo.currentProgress]);
                            });
                        }
                    });
                }
            }

            $('body').find('.session-single__close').removeClass('question-close');
            $('body').css('overflow', 'hidden');
            $('.session-single').fadeIn(250, function () {
                $('body').trigger('session.watch.open');
            });

            var url = new URL(window.location.href);
            var params = new URLSearchParams(url.search.slice(1));
            if(params.has("session")) {
                params.delete("session");
            }

            params.append("session", $(e.target).data('session-id'));

            window.history.replaceState("", "", "?" + params.toString());
        });
    });

    $('body').on('click', '.session-single__close', function (e) {
        e.preventDefault();

        var activeVideo = getVideoDetails();

        if (activeVideo)
            activeVideo.video.pause();

        $('body').css('overflow', 'initial');
        $('.session-single').fadeOut();

        var url = new URL(window.location.href);
        var params = new URLSearchParams(url.search.slice(1));
        params.delete("session");

        window.history.replaceState("", "", "?");
    });

    window.addEventListener('beforeunload', function (e) {
        var activeVideo = getVideoDetails();

        if (activeVideo)
            $('body').trigger('session.watch.stop', [activeVideo.session, activeVideo.video, activeVideo.progress]);
    });

    $('body').on('session.watch.stop', function (e, session_id, video, progress) {
        var activeVideo = getVideoDetails(),
            videoWrap = activeVideo.popupWrap.find('.session-single__video');

        if (videoWrap) {
            var ajaxData = {
                id: session_id,
                progress: progress
            };

            $.ajax({
                type: 'POST',
                url: videoWrap.data('route'),
                data: ajaxData,
                success: function (res) {
                    if (!activeVideo)
                        return;

                    var updated = videoWrap.data('updated');

                    videoWrap.data('updated', updated + progress);
                }
            });
        }
    });

    /**
     * Check if there is an session ID in url, and auto-open that popup
     */
    if (typeof getUrlParameter('session') != 'undefined') {
        var sessionId = getUrlParameter('session'),
            sessionWrap = $('body').find('#session-' + sessionId);

        if (sessionWrap.length)
            sessionWrap.find('.js-open-session').trigger('click');
    }

    /**
     * Course Progress
     */
    $('body').on('click', '.js-header-progress', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $('.dropdown-menu-wrap, .masthead__notifications-outer-wrap, .masthead__classes-wrap').stop().slideUp(200);
        $('.course-progress-box').stop().slideToggle(200);
    });

    $('body').on('click', '.js-header-close-progress', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $('.course-progress-box').stop().slideUp(200);
    });

    $('body').on('click', '.course-progress-box', function (e) {
        e.stopPropagation();
    });

    var timerToClose;
    $('.course-progress-box__item--lesson-mark').on({
        mouseenter: function () {
            clearTimeout(timerToClose);

            $('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
            $(this).addClass('is-hover');
            $(this).next('.course-progress-box__item--lesson-mark__info').addClass('is-hover');
        },
        mouseleave: function () {
            timerToClose = window.setTimeout(function () {
                $('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
            }, 200);
        }
    });

    $('.course-progress-box__item--lesson-mark__info').on({
        click: function (e) {
            e.stopPropagation();
        },
        mouseenter: function () {
            clearTimeout(timerToClose);

            $('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
            $(this).addClass('is-hover');
            $(this).prev('.course-progress-box__item--lesson-mark').addClass('is-hover');
        },
        mouseleave: function () {
            timerToClose = window.setTimeout(function () {
                $('.course-progress-box__item--lesson-mark__info, .course-progress-box__item--lesson-mark').removeClass('is-hover');
            }, 200);
        }
    });

    /**
     * Events Calendar
     */
    $("#datepicker").datepicker({
        numberOfMonths: 2,
        dayNamesMin: ["S", "M", "T", "W", "T", "F", "S"],
        beforeShowDay: calendarMarkDays,
        onSelect: changeDate
    });

    function changeDate(_date) {
        var date = new Date(_date);

        if ($.inArray($.datepicker.formatDate('yy-mm-dd', date), $.parseJSON(window.calendar_events)) === -1) {
            return;
        }

        var ajaxData = {
            date: _date
        };

        $('body').createLoading();

        $.ajax({
            type: 'GET',
            url: 'calendar/date',
            data: ajaxData,
            success: function (res) {
                $('body').find('.events').replaceWith(res);
                $('body').removeLoading();
            }
        });
    }

    function calendarMarkDays(date) {
        var now = new Date();

        if ($.inArray($.datepicker.formatDate('yy-mm-dd', date), $.parseJSON(window.calendar_events)) > -1) {
            if (now < date) {
                return [true, 'has-event'];
            } else {
                return [true, 'had-event'];
            }
        }

        return [true, ''];
    }

    function updateCalendarMarkers(dates) {
        $("#datepicker").find('.has-event').removeClass('has-event');
        $("#datepicker").find('.had-event').removeClass('had-event');

        window.calendar_events = dates;

        $("#datepicker").datepicker('option', 'beforeShowDay', calendarMarkDays);
    }

    $('body').on('change', '.js-calendar-filter', function (e) {
        e.preventDefault();

        var $this = $(this),
            course = $this.find('option:selected').val();

        var ajaxData = {
            course: course
        };

        $('body').createLoading();

        $.ajax({
            type: 'GET',
            url: $this.data('route'),
            data: ajaxData,
            success: function (res) {
                $('body').find('.events').replaceWith(res.view);
                updateCalendarMarkers(res.events);
                $('body').removeLoading();
            }
        });
    });

    /**
     * Circular Progress
     */
    $(window).on('load', function () {
        $('.course-progress__bar--active').each(function () {
            var percent = $(this).data("percentage");

            var circle = new ProgressBar.Circle($(this)[0], {
                color: '#62D262',
                strokeWidth: 18.75
            });

            circle.animate(percent);  // Number from 0.0 to 1.0
        });

        $('.module__active').each(function () {
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
    $('.js-count-chars').on('keyup', function () {
        var $this = $(this),
            text_max = $this.attr('maxlength'),
            text_length = $this.val().length,
            text_remaining = text_max - text_length,
            charsShow = $this.data('chars');

        $('.chars-count[data-chars="' + charsShow + '"] span').html(text_remaining);
    });

    $('body').on('click', '.course-progress:not(.course-progress__lesson)', function () {
        var $this = $(this),
            completeHtml = '<div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>';

        if ($this.is('.doing')) {
            return;
        }

        $this.addClass('doing');

        $.ajax({
            url: $this.data('complete')
        }).always(function (res) {
            if (typeof res == 'object' && res.lesson_complete == true) {
                $('body').find('.js-bonus').show();
            }

            dataLayer.push({
                'event': 'completed',
                'module': res.module.title,
                'lesson': res.lesson.title,
                'session': res.session.title,
                'course': res.course.title
            });

            if (res.lesson_complete) {
                dataLayer.push({
                    'event': 'completedLesson',
                    'module': res.module.title,
                    'lesson': res.lesson.title,
                    'course': res.course.title
                });
            }

            if (res.module_complete) {
                dataLayer.push({
                    'event': 'completedModule',
                    'module': res.module.title,
                    'course': res.course.title
                });
            }

            if (res.course_complete) {
                dataLayer.push({
                    'event': 'completedCourse',
                    'course': res.course.title
                });
            }

            var sId = $this.closest('.session-single__content-ajax').find('.session-single__video').data('session'); // Get session id
            $this.replaceWith(completeHtml);

            if (typeof sId != 'undefined') {
                $('body').find('#session-' + sId).find('.course-progress').replaceWith(completeHtml);
                mixpanel.track("Session completed", {
                    "Video length": 213,
                    "id": "hY7gQr0"
                });
            }

            $this.removeClass('doing');
            window.location.reload(true);
        });
    });

    /**
     * Post to facebook on lesson view.
     * Delay the submit
     */
    $('body').find('form.js-lesson-post-to-facebook').on('submit', function (e) {
        e.preventDefault();

        var frm = $(this),
            fburl = frm.data('fburl');

        if (frm.is('.doing'))
            return false;

        frm.addClass('doing');

        var win = window.open(fburl, '_blank');
        win.focus();

        setTimeout(function () {
            frm.off('submit');
            frm.submit();
        }, 3000);

        return false;
    });

    /**
     * Survey popup
     */
    $('body').on('click', '.survey-popup__navigation a', function (e) {
        e.preventDefault();

        var $this = $(this),
            surPopupWrap = $this.closest('.survey-popup'),
            form = surPopupWrap.find('form'),
            currentStep = surPopupWrap.find('.survey-popup__step:visible'),
            nextBtn = form.find('.survey-popup__navigation__next'),
            prevBtn = form.find('.survey-popup__navigation__prev'),
            subBtn = form.find('.survey-popup__navigation__submit'),
            stepIndex = form.find('.survey-popup__step').index(currentStep);

        if ($this.is('.next')) {
            var stepToShow = currentStep.next('.survey-popup__step');

            if (!form.parsley().validate({group: 'block-' + stepIndex}))
                return false;
        } else {
            var stepToShow = currentStep.prev('.survey-popup__step');
        }

        if (stepToShow.is(form.find('.survey-popup__step').first())) {
            nextBtn.show();
            prevBtn.hide();
            subBtn.hide();
        } else if (stepToShow.is(form.find('.survey-popup__step').last())) {
            nextBtn.hide();
            prevBtn.show();
            subBtn.show();
        } else {
            nextBtn.show();
            prevBtn.show();
            subBtn.hide();
        }

        currentStep.fadeOut(250, function () {
            stepToShow.fadeIn(250);
        });
    });

    $('body').on('click', '.js-open-surveyPopup', function () {
        var popup = $('body').find('.survey-popup');

        if (popup.length)
            popup.css('display', 'flex').hide().fadeIn(250);

        return false;
    });

    $('body').on('click', '.survey-popup__close', function (e) {
        e.preventDefault();

        $(this).parents('.survey-popup').fadeOut(250);
    });

    /**
     * User billing
     */
    $('body').on('click', '.js-open-billing-details', function (e) {
        e.preventDefault();

        $(this).parent().find('.billing-course__details').stop().slideToggle(250);
    });

    $('.js-stripe-cc-num').payment('formatCardNumber');
    $('.js-stripe-cc-expiration').payment('formatCardExpiry');

    $('body').on('submit', '.billing-course__ccard__form', function (e) {
        e.preventDefault();

        var frm = $(this);

        if (frm.is('.doing')) {
            return false;
        }

        if (frm.find('[type="submit"]').is('.edit')) {
            frm.find('[type="submit"]').val('Update credit card');
            frm.find('[type="submit"]').removeClass('edit');

            frm.find('.form-control__noteditable').removeClass('form-control__noteditable');
            frm.find('.form-control').first().find('input').focus();

            frm.find('[name="cc_number"], [name="cc_expiration"]').val('');
            return false;
        } else {
            frm.find('[type="submit"]').val('Edit Billing Details');
            frm.find('[type="submit"]').addClass('edit');
        }

        var ccNum = frm.find('[name="cc_number"]'),
            ccName = frm.find('[name="nameoncard"]'),
            ccExpiry = frm.find('[name="cc_expiration"]'),
            ccAddress = frm.find('[name="billing_address"]'),
            course_id = frm.closest('.billing-course').attr('id').replace(/^\D+/g, '');

        var validCC = $.payment.validateCardNumber(ccNum.val());
        if (!validCC) {
            alert('Your card is not valid!');
            return false;
        }

        var ccExpiryExtracted = ccExpiry.payment('cardExpiryVal');

        if (ccName.val().length == 0) {
            alert('Enter your name on card');
            return false;
        }

        var ajaxData = {
            cc_name: ccName.val(),
            cc_address: ccAddress.val(),
            cc_number: ccNum.val(),
            cc_expiry_month: ccExpiryExtracted.month,
            cc_expiry_year: ccExpiryExtracted.year,
            course_id: course_id
        };

        frm.addClass('doing');
        $('body').createLoading();

        $.ajax({
            type: 'POST',
            url: frm.attr('action'),
            data: ajaxData,
            success: function (res) {
                frm.removeClass('doing');
                $('body').removeLoading();

                if (res.status) {
                    $('body')
                        .find('.ask-alert')
                        .removeClass('ask-alert--critical')
                        .addClass('ask-alert--success')
                        .show()
                        .html(res.message);

                    frm.find('.form-control').addClass('form-control__noteditable');
                } else {
                    $('body')
                        .find('.ask-alert')
                        .removeClass('ask-alert--success')
                        .addClass('ask-alert--critical')
                        .show()
                        .html(res.message);
                }
            }
        });
    });

    /**
     * Calendar event
     */
    $('body').on('click', '.js-open-event', function (e) {
        e.preventDefault();

        var $this = $(this),
            url = this.href;

        $.ajax({
            url: url
        }).always(function (res) {
            $('.event-single__content-ajax').html(res);

            $('body').css('overflow', 'hidden');
            $('.event-single').fadeIn();
        });
    });

    $('body').on('click', '.event-single__close', function (e) {
        e.preventDefault();

        $('body').css('overflow', 'initial');
        $('.event-single').fadeOut();
    });

    /**
     * Mobile menu
     */
    $('body').on('click', '.mobile-menu__open', function (e) {
        e.stopPropagation();

        if ($('.mobile-menu').hasClass('mobile-menu__show')) {
            $('.mobile-menu').removeClass('mobile-menu__show');
        } else {
            $('.mobile-menu').addClass('mobile-menu__show');
        }
    });

    $('body').on('click', '.mobile-menu__close', function (e) {
        $('.mobile-menu').removeClass('mobile-menu__show');
    });

    $('body').on('click', '.mobile-menu', function (e) {
        e.stopPropagation();
    });

    $('body').on('click', function (e) {
        $('.mobile-menu').removeClass('mobile-menu__show');
    });

    /**
     * Alerts
     */
    $('body').on('click', '.js-close-ask-alert', function (e) {
        e.preventDefault();

        var el = $(this),
            wrapper = el.closest('.ask-alert'),
            url = wrapper.data('key');

        $.ajax({
            url: url
        }).always(function (res) {
            wrapper.fadeOut(250);
        });
    });

    /**
     * Customer support
     */
    $('body').on('click', '.js-contact-customer-service', function (e) {
        e.preventDefault();
        HS.beacon.open();
    });

    /**
     * Mixpanel tracks
     */
    $('body').on('click', '.js-fb-group', function (e) {
        var tl = mixpanelTrackLinks($(this));
        e.preventDefault();
        mixpanel.track('Clicked on Facebook group', {'URL': $(this).attr('href')});
        setTimeout(tl, 500);

        $.ajax({
            type: 'POST',
            url: '/log',
            data: {
                'action_id' : 3,
                'activity_id' : 8
            },
            success: function (res) {

            }
        });
    });

    $('body').on('click', '.js-event-apply', function (e) {
        var tl = mixpanelTrackLinks($(this));
        e.preventDefault();
        mixpanel.track('Clicked on Apply Event');
        setTimeout(tl, 500);

        $.ajax({
            type: 'POST',
            url: '/log',
            data: {
                'action_id' : 3,
                'activity_id' : 9
            },
            success: function (res) {

            }
        });
    });

    function mixpanelTrackLinks(a) {
        return function () {
            if (a.attr('target') == '_blank') {
                var win = window.open(a.attr('href'), '_blank');
                win.focus();
            } else {
                window.location = a.attr('href');
            }
        }
    }
});
