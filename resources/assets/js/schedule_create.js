var type = "";

if(window.location.href.indexOf('edit') === -1) {
    ajaxCall();
}

$('#schedule_type, #course_id').on('change',function () {
    ajaxCall();
});

function ajaxCall()
{
    type = $('#schedule_type').val();

    $.ajax({
        url: "/schedule/create_next",
        type: "post",
        data: {
            'course_id': $('#course_id').val(),
            'schedule_type' : $('#schedule_type').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function success(response) {
            var module = $('#modules_lessons');
            module.empty();
            module.append('<label>Modules and Lessons</label>');
            $.each(response.modules,function () {
                module.append(htmlGeneratorModule(this));
                $.each(this.lessons, function () {
                    module.append(htmlGeneratorLesson(this));
                })
            })
        },
        error: function error(_error) {
            console.log(_error);
        }
    });
}

function htmlGeneratorModule(module) {
    var html =  '';

    if (type === "dripped") {
        html += '<div class="form-group"><div class="input-group"><span class="input-group-addon" id="basic-addon3"><b>Module</b>: '+ module.title +'</span>';
        html += '<input type="number" min="0" class="form-control" name="modules['+module.id+']" id="module_'+module.id+'" aria-describedby="basic-addon3" required>';
    } else {
        html += '<div class="form-group"><div class="input-group"><span class="input-group-addon" id="basic-addon3"><b>Module</b>: '+ module.title +'</span>';
        html += '<input type="datetime-local" class="form-control" name="modules['+module.id+']" id="module_'+module.id+'" aria-describedby="basic-addon3" required><div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>';
    }

    html += '</div>'+
            '</div>';

    return html;
}

function htmlGeneratorLesson(lesson) {
    var html =  '';
    if (type === "dripped") {
        html += '<div class="form-group" ><div class="input-group col-md-offset-1"><span class="input-group-addon" id="basic-addon3"><b>Lesson</b>: '+ lesson.title +'</span>';
        html += '<input type="number" min="0" class="form-control" name="lessons['+lesson.id+']" id="lesson_'+lesson.id+'" aria-describedby="basic-addon3" required>';
    } else {
        html += '<div class="form-group"><div class="input-group col-md-offset-1"><span class="input-group-addon" id="basic-addon3"><b>Lesson</b>: '+ lesson.title +'</span>';
        html += '<input type="datetime-local" class="form-control" name="lessons['+lesson.id+']" id="lesson_'+lesson.id+'" aria-describedby="basic-addon3" required><div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>';
    }

    html += '</div>'+
        '</div>';

    return html;

}


