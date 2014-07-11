$(document).ready(function(){

    $("input[type='text']").focus(function() {
        if (this.value === this.defaultValue) {
            this.value = '';

            var attr = $(this).attr('data-password');

            if (typeof attr !== typeof undefined && attr !== false) {
                this.type = 'password';
            }

        }

    }).blur(function() {
            if (this.value === '') {
                this.value = this.defaultValue;

                var attr = $(this).attr('data-password');

                if (typeof attr !== typeof undefined && attr !== false) {
                    this.type = 'text';
                }
            }

        });

    resize();

    $(window).resize(function () {
        resize();
    });

    function resize(){
        var $content = $(".height-100");

        $.each($content, function(){

            var $height = $(window).height() - (parseInt($(this).css("padding-top")) + parseInt($(this).css("padding-bottom")));

            $(this).css({"min-height": $height});

        });

    }
});