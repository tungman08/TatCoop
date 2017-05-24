$.fn.getType = function () { return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); }

Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

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