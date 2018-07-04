$(document).ready(function () {
    //chart Declaration
    Chart.defaults.global.legend.position = 'bottom';

    var firstTimeModule = true;
    var firstTimeLesson = true;
    var firstTimeSession = true;

    var inputFields = [];
    var colorPallete = [];

    var modulesChart = '';
    var lessonChart = '';
    var sessionChart = '';

    $('#btnSync').on('click',function () {
        $.ajax({
            url: "dashboard/cache",
            type: "get",
            data: {

            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function success(response) {
                console.log(response);
            },
            error: function error(_error) {
                console.log(_error);
            }
        });
    });


    $.ajax({
        url: "dashboard/fields",
        type: "get",
        data: {

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(response) {
            inputFields = response;
            fillFields();
            getPieChartsData();
        },
        error: function error(_error) {
            console.log(_error);
        }
    });

    $('#course_id').on('change',function () {
        addBlurAndLoading();
        fillCohorts($('#course_id :selected').val());
        fillModules($('#course_id :selected').val());
        $('#lesson_id').empty();
        $('#lesson_id').append("<option value='' > Select a lesson </option>");
        getPieChartsData();
    });

    $('#cohort_id').on('change',function () {
        addBlurAndLoading();
        getPieChartsData();
    });

    $('#module_id').on('change',function () {
        addBlurAndLoading();
        if($('#module_id :selected').val() !== "") {
            fillLessons($('#module_id :selected').val());
        } else {
            $('#lesson_id').empty();
            $('#lesson_id').append("<option value='' > Select a lesson </option>");
        }
        getPieChartsData();
    });

    $('#lesson_id').on('change',function () {
        addBlurAndLoading();
        getPieChartsData();
    });

    function fillFields() {
        inputFields.forEach(function (inp) {
            $('#course_id').append("<option value='"+inp.id+"'>"+inp.title+"</option>")
        });

        fillCohorts(inputFields[0].id);
        fillModules(inputFields[0].id);
    }

    function fillCohorts(course_id) {
        $('#cohort_id').empty();
        $('#cohort_id').append("<option value=''> Select a cohort </option>");

        inputFields.find(function(el){
            return el.id == course_id;
        }).cohorts.forEach(function (coh) {
            $('#cohort_id').append("<option value='"+coh.id+"'>"+coh.name+"</option>");
        });
    }

    function fillModules(course_id) {
        $('#module_id').empty();
        $('#module_id').append("<option value=''> Select a module </option>");

        inputFields.find(function(el){
            return el.id == course_id;
        }).modules.forEach(function (mod) {
            $('#module_id').append("<option value='"+mod.id+"'>"+mod.title+"</option>");
        });
    }

    function fillLessons(module_id) {
        $('#lesson_id').empty();
        $('#lesson_id').append("<option value='' > Select a lesson </option>");

        inputFields.find(function(el){
            return el.modules.find(function (mod) {
                return mod.id == module_id;
            });
        }).modules.find(function (mod) {
            return mod.id == module_id;
        }).lessons.forEach(function (les) {
            $('#lesson_id').append("<option value='"+les.id+"'>"+les.title+"</option>");
        })
    }

    function getPieChartsData() {
        $.ajax({
            url: "dashboard/data",
            type: "get",
            data: {
                'course_id': $('#course_id :selected').val(),
                'cohort_id': $('#cohort_id :selected').val(),
                'module_id': $('#module_id :selected').val(),
                'lesson_id': $('#lesson_id :selected').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function success(response) {
                if(response === 'error') {
                    $('#modules, #lessons, #sessions, #moduleLegend').css('display','none');
                    $('#error_chart').css('display','inherit');
                } else {
                    $('#error_chart').css('display','none');
                    $('#modules, #lessons, #sessions, #moduleLegend').css('display','block');

                    if( colorPallete.length === 0) {
                        colorPallete = response[5];
                    }

                    window.piedata = response;

                    createPieCharts(response);
                }

                $('#overlay_loading').css('display','none');
                $('#blurDiv').css('filter','');
            },
            error: function error(_error) {
                console.log(_error);
            }
        });
    }

    function createPieCharts(data) {
        if(data.length === 0) {
            $('#lessons, #lessonLegend').css('display','none');
            $('#sessions, #sessionLegend').css('display','none');
            $('#error_chart').css('display','inherit');
            return true;
        }

        if(!firstTimeModule) {
            modulesChart.data.labels = Object.keys(data[0]);
            modulesChart.data.datasets[0].data = Object.values(data[0]);
            modulesChart.update();

            generateModuleLegend(data);

        } else {
            var ctx = $('#modules');
            modulesChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(data[0]),
                    datasets: [{
                        label: '% Completed',
                        data: Object.values(data[0]),
                        backgroundColor: colorPallete.slice(0,Object.keys(data[0]).length)
                    }]
                },
                options: {
                    title: {
                        display: 'true',
                        text: 'Modules'
                    },
                    legend: {
                        display: false
                    }
                }
            });

            generateModuleLegend(data);

            firstTimeModule = false;
        }

        if(data[1].length !== 0) {
            if(!firstTimeLesson) {
                $('#lessons, #lessonLegend').css('display','inherit');

                lessonChart.data.labels = Object.keys(data[1]);
                lessonChart.data.datasets[0].data = Object.values(data[1]);
                lessonChart.update();

                generateLessonLegend(data);
            } else {
                $('#lessons, #lessonLegend').css('display','inherit');

                var ctx1 = $('#lessons');
                lessonChart = new Chart(ctx1, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(data[1]),
                        datasets: [{
                            label: '% Completed',
                            data: Object.values(data[1]),
                            backgroundColor: colorPallete.slice(0,Object.keys(data[1]).length)
                        }]
                    },
                    options: {
                        title: {
                            display: 'true',
                            text: 'Lessons'
                        },
                        legend: {
                            display: false
                        }
                    }
                });
                generateLessonLegend(data);

                firstTimeLesson = false;
            }
        } else {
            $('#lessons, #lessonLegend').css('display','none');
            $('#error_chart').css('display','inherit');
        }

        if(data[2].length !== 0) {
            if(!firstTimeSession){
                $('#sessions, #sessionLegend').css('display','inherit');

                sessionChart.data.labels = Object.keys(data[2]);
                sessionChart.data.datasets[0].data = Object.values(data[2]);
                sessionChart.update();

                generateSessionLegend(data);
            } else {
                $('#sessions, #sessionLegend').css('display','inherit');

                var ctx3 = $('#sessions');
                sessionChart = new Chart(ctx3, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(data[2]),
                        datasets: [{
                            label: '% Completed',
                            data: Object.values(data[2]),
                            backgroundColor: colorPallete.slice(0,Object.keys(data[2]).length)
                        }]
                    },
                    options: {
                        title: {
                            display: 'true',
                            text: 'Sessions'
                        },
                        legend: {
                            display: false
                        }
                    }
                });

                generateSessionLegend(data);

                firstTimeSession = false;
            }
        } else {
            $('#sessions, #sessionLegend').css('display','none');
            $('#error_chart').css('display','inherit');
        }



    }

    function generateModuleLegend(data) {
        var html = "<ul style='list-style: none; font-size: 24px'>";
        Object.keys(data[0]).forEach(function (module) {
            html += "<li>"+module+" "+data[6][module]+"% - Avg "+data[3][module]+" days</li>";
        });
        html += "</ul>";

        $('#moduleLegend').html(html);
    }

    function generateLessonLegend(data) {
        var html = "<ul style='list-style: none; font-size: 24px'>";
        Object.keys(data[1]).forEach(function (lesson) {
            html += "<li>"+lesson+" "+data[7][lesson]+"% - Avg "+data[4][lesson]+" days</li>";
        });
        html += "</ul>";

        $('#lessonLegend').html(html);
    }

    function generateSessionLegend(data) {
        var html = "<ul style='list-style: none; font-size: 24px'>";
        Object.keys(data[2]).forEach(function (session) {
            html += "<li>"+session+" "+data[8][session]+"% </li>";
        });
        html += "</ul>";

        $('#sessionLegend').html(html);
    }

    function addBlurAndLoading() {
        $('#overlay_loading').css('display','inherit');
        $('#blurDiv').css('filter','blur(3px)');
    }

});