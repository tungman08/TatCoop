$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('#province_id').change(function () {
        $.ajax({
            url: '/ajax/districts',
            type: "post",
            data: {
                'id': $('#province_id').val()
            },
            success: function (districts) {
                $("#district_id").empty();

                $.each(districts, function (i, district) {
                    $("#district_id").append($("<option></option>").val(this.id).html(this.name));
                });

                getSubdistrict();
            }
        });
    });

    $('#district_id').change(function () {
        getSubdistrict();
    });

    $('#subdistrict_id').change(function () {
        getPostcode();
    });
});

function getSubdistrict() {
    $.ajax({
        url: '/ajax/subdistricts',
        type: "post",
        data: {
            'id': $('#district_id').val()
        },
        success: function (subdistricts) {
            $("#subdistrict_id").empty();

            $.each(subdistricts, function (i, subdistrict) {
                $("#subdistrict_id").append($("<option></option>").val(this.id).html(this.name));
            });

            getPostcode();
        }
    });
}

function getPostcode() {
    $.ajax({
        url: '/ajax/postcode',
        type: "post",
        data: {
            'id': $('#subdistrict_id').val()
        },
        success: function (postcode) {
            $("#postcode").val(postcode);
        }
    });
}