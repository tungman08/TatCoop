$.fn.getType = function () { return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); }

function change_skin(skin) {
    $.ajax({
        url: '/ajax/skin',
        type: "post",
        data: {
            'skin': skin,
            '_token': $("input[name='_token']").val()
        },
        success: function (skins) {
            $.each(skins, function (i, skin) {
                $("body").removeClass(skin.code);
            });

            $('body').addClass(skin);
        }
    });
}