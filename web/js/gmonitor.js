var updateInterval = 2000;


function render_fuction_table()
{
    var tpl =_.template($('#functions_table_template').html());
    $('#functions_table_wrap').html(tpl());
    setInterval(function () {
        $.ajax({
            url: '/api/function_status/',
            dataType: 'json',
            success: function (resp) {
                var tpl2 = _.template($('#function_rows').html());
                if (resp.result === 'ok') {
                    $('#functions_tbody').html(tpl2({rdata: resp.data}));
                }
                reset_function_queue();
            }
        })
    }, updateInterval)
}

function worker_count()
{
    setInterval(function () {
        $.ajax({
            url: '/api/worker_count/',
            dataType: 'json',
            success: function (resp) {
                $('#worker_count').text(resp);
            }
        })
    }, updateInterval);
}

function worker_add()
{
    $('#worker_add').click(function () {
        $.ajax({
            url: '/api/worker_start/',
            type: 'get',
            data: {
                count: $('input#worker_start_count').val()
            },
            success: function (resp) {
                if (resp === 'ok') {
                    $.alert('Worker(s) Added');
                }
                else {
                    console.log(resp);
                    $.alert('Some Error Occured, view to browser console');
                }
            }
        })
    })
}

function worker_set_count()
{
    $('#worker_set_count').click(function () {
        var worker_count = $('input#worker_set_count_val').val();
        $.ajax({
            url: '/api/worker_stop/',
            success: function (resp) {
                if (resp === 'ok') {
                    $.ajax({
                        url: '/api/worker_start/',
                        type: 'get',
                        data: {
                            count: worker_count
                        },
                        success: function (resp) {
                            if (resp === 'ok') {
                                $.alert('Count of Workers Set to ' + worker_count);
                            }
                            else {
                                console.log(resp);
                                $.alert('Worker Start Error, view to browser console');
                            }
                        }
                    })
                }
                else {
                    console.log(resp);
                    $.alert('Worker Stop Error, view to browser console');
                }
            }
        })
    })
}

function worker_stop()
{
    $.ajax({
        url: '/api/worker_stop/',
        success: function (resp) {
            if (resp === 'ok') {
                $.alert('All Workers Stopped');
            }
            else {
                console.log(resp);
                $.alert('Some Error Occured, view to browser console');
            }
        }
    })
}

function worker_stop_button_bind() {
    $('#worker_stop').click(function () {
        worker_stop();
    });
}

function reset_function_queue()
{
    $('.reset')
        .unbind()
        .click(function () {
            var function_name = $(this).attr('data-function');
            $.ajax({
                url: '/api/function_queue_reset/',
                data: {
                    function_name: function_name
                },
                success: function (resp) {
                    if (resp === 'ok') {
                        $.alert('Queue of function "' + function_name + '" reset');
                    }
                    else {
                        if(resp === 'empty') {
                            $.alert('Queue of function "' + function_name + '"is EMPTY, reset not need');
                        }
                        else {
                            console.log(resp);
                            $.alert('Some Error Occured, view to browser console');
                        }
                    }
                }
            });
    })
}

function reset_all_queue()
{
    $.ajax({
        url: '/api/reset_all_queue/',
        success: function (resp) {
            if (resp === 'ok') {
                $.alert('All Functions Queue Reset');
            }
            else {
                console.log(resp);
                $.alert('Some Error Occured, view to browser console');
            }
        }
    })
}

function reset_all_queue_button_bind() {
    $('#reset_all_functions').click(function () {
        reset_all_queue();
    })
}

function total_reset_button_bind () {
    $('#total_reset').click(function () {
        reset_all_queue();
        worker_stop();
    })
}

$(document).ready(function () {
    render_fuction_table();
    worker_count();
    worker_add();
    worker_set_count();
    worker_stop_button_bind();
    reset_all_queue_button_bind();
    total_reset_button_bind();
});