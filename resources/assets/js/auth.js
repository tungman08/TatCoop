$(function() {
    $('[data-tooltip="true"]').tooltip();
    $("[data-mask]").inputmask();
});

$('form').submit(function() {
    $("[data-mask]").inputmask('remove');
    $('#member_id').val(parseInt($('#member_id').val()));
});
