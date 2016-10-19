$('#genpassword').click(function () {
    $.ajax({
        url: '/ajax/password',
        type: "post",
        data: {
            '_token': $("input[name='_token']").val()
        },
        beforeSend: function () {
            $('#new_password').val('\u231B กรุณารอสักครู่...');
        },
        success: function (msg) {
            $('#new_password').val(msg);
        }
    });
});