$(document).ready(function(){

    $("input[type='text'], input[type='hidden']").focus(function() {
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
    toggleDisplayMessageBox();

    $(window).resize(function () {
        resize();
    });

    $(document).on("mousedown", "[data-icon]", function (e) {

        disabledEventPropagation(e);

        var $data_id = $(this).attr('data-id');

        var $menu_item = $("[data-menu-id='"+$data_id+"']");

        toggleMenuItemOverlay($menu_item);

    });

    $(document).on("mousedown", "#forgotPass", function (e) {

        var $confirm = $('[name="confirm"]');
        var $password = $('[name="password"]');
        var $submitType = $('[name="submitType"]');

        disabledEventPropagation(e);

        var $button = $('[data-button-type="register"]');

        $button.attr('data-button-type', "login");
        $button.html("Login");

        $confirm.val($confirm.prop("defaultValue"));
        $password.val($password.prop("defaultValue"));

        $confirm.attr("type", "hidden");
        $password.attr("type", "hidden");
        $submitType.val("2");

        $(this).hide();

    });

    $(document).on("mousedown", ".ui-message-close", function (e) {

        toggleDisplayMessageBox();

    });

    $(document).on("mousedown", "[data-button-type]", function (e) {

        disabledEventPropagation(e);

        toggleDataButtonType($(this));

    });

});

function toggleDisplayMessageBox(){

    var $box = $(".ui-message-box");
    var $background = $(".ui-message-background");

    if($background.hasClass("hidden") && $box.attr("data-type") === "overlay"){

        $background.removeClass("hidden");

        setTimeout(function(){
            $background.css("opacity", "1");
        }, 50);

    }else if(!$background.hasClass("hidden") && $box.attr("data-type") === "overlay" && parseInt($background.css("opacity")) > 0){

        $background.css("opacity", "0");

        setTimeout(function(){
            $background.addClass("hidden");
        }, 300);
    }

    if(parseInt($box.css("top")) < 0)
        $box.css("top", calculateTop($box));
    else
        $box.css("top", "-400px");

}

function calculateTop($object){

    var $height = ($(window).outerHeight()/2) - ($object.outerHeight()/2);

    if($height <= 0){
        $height = 0;
    }

    return $height;

}

function toggleDataButtonType($button){

    var $confirm = $('[name="confirm"]');
    var $password = $('[name="password"]');
    var $submitType = $('[name="submitType"]');

    if($button.length == 0)
        return false;

    if($button.attr('data-button-type') === "register"){

        $button.attr('data-button-type', "login");
        $button.html("Login");

        if ($password.val() === $password.prop("defaultValue") || $password.val().length === 0) {
            $password.attr("type", "text");
        }

        if ($confirm.val() === $confirm.prop("defaultValue") || $confirm.val().length === 0) {
            $confirm.attr("type", "text");
        }else{
            $confirm.attr("type", "password");
        }

        $submitType.val("1");

    }else if($button.attr('data-button-type') === "login"){

        $button.attr('data-button-type', "register");
        $button.html("Register");

        if ($password.val() === $password.prop("defaultValue") || $password.val().length === 0) {
            $password.attr("type", "text");
        }

        $confirm.val($confirm.prop("defaultValue"));
        $confirm.attr("type", "hidden");

        $submitType.val("0");

        $("#forgotPass").show();

    }

    return true;
}

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