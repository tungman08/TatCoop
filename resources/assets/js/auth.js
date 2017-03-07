var min = 0;
var max = 4;

$(document).ready(function () {
    $('[data-tooltip="true"]').tooltip();
    $("[data-mask]").inputmask();
    $("header").hide();
    background(min);
});

$('form').submit(function() {
    $("[data-mask]").inputmask('remove');
    $('#member_id').val(parseInt($('#member_id').val()));
});

function background(date) {
    $.ajax({
        url: "/ajax/background",
        type: "get",
        cache: true,
        data: {
            "date": date
        },
        beforeSend: function () {
            $("header").show();
            $("body").css("backgroundImage", "none");
            $("#copyright").html('Please wait...');
            $("#copyrightlink").attr("href", '#');
        },
        success: function (image) {
            $("body").css("backgroundImage", "url('/background/" + moment(image.background_date).format('YYYYMMDD') + ".jpg')").waitForImages({
                waitForAll: true,
                finished: function() {
                    $("header").hide();
                }
            });
            $("#copyright").html(image.copyright);
            $("#copyrightlink").attr("href", image.copyrightlink);
        },
    });
}

$("#previous").click(function () {
    $date = (parseInt($(this).attr('data-selected'), 10) < max) ? parseInt($(this).attr('data-selected'), 10) + 1 : min;

    $(this).attr('data-selected', $date);
    $("#next").attr('data-selected', $date);

    background($date);
});

$("#next").click(function () {
    $date = (parseInt($(this).attr('data-selected'), 10) > min) ? parseInt($(this).attr('data-selected'), 10) - 1 : max;

    $("#previous").attr('data-selected', $date);
    $(this).attr('data-selected', $date);

    background($date);
});
