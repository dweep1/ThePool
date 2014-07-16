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
    hideMenuItems();

    $(window).resize(function () {
        resize();
    });

    $(document).on("mousedown", "[data-icon]", function (e) {

        disabledEventPropagation(e);

        var $data_id = $(this).attr('data-id');

        var $menu_item = $("[data-menu-id='"+$data_id+"']");

        toggleMenuItemOverlay($menu_item);

    });

    $(document).on("mousedown", "[data-button-type]", function (e) {

        disabledEventPropagation(e);

        if($(this).attr('data-button-type') === "register"){

            $(this).attr('data-button-type', "login");
            $(this).html("Login");
            $('[name="confirm"]').attr("type", "text");
            $('[name="submitType"]').value("1");

        }else if($(this).attr('data-button-type') === "login"){

            $(this).attr('data-button-type', "register");
            $(this).html("Register");
            $('[name="confirm"]').attr("type", "hidden");
            $('[name="submitType"]').value("0");

        }

    });

});

function toggleMenuItemOverlay($menu_item){

    if($menu_item.hasClass("hidden")){

        $menu_item.css("display", "block");

        setTimeout(function(){
            $menu_item.removeClass("hidden");
        }, 50);

    }else{
        $menu_item.addClass("hidden");

        setTimeout(function(){
            $menu_item.css("display", "block");
        }, 300);
    }

}

function resize(){
    var $content = $(".height-100");

    $.each($content, function(){

        var $height = $(window).height() - (parseInt($(this).css("padding-top")) + parseInt($(this).css("padding-bottom")));

        $(this).css({"min-height": $height});

    });
}

function hideMenuItems(){

    var $content = $("[data-menu='hidden']");

    $.each($content, function(){

        $(this).css({"display": "none"});

    });

}

function disabledEventPropagation(event){

    if (event.stopPropagation){

        event.stopPropagation();

    }

}