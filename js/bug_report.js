$(document).ready(function(){
    var $body = $('body');

    var $icon = '<i style="position: fixed; bottom: 30px; right: 50px;' +
        'padding 20px; font-size:40px; color: rgba(44,44,44,0.8); text-shadow: 0px 0px 2px #fff; cursor:pointer" ' +
        'class="fa fa-bug bug-ui-report"></i>';

    var $popup = '<div id="bugReportForm" style="position: fixed; z-index: 9999;' +
        '-webkit-transition: all 1.2s ease; padding:20px; -moz-transition: all 1.2s ease; -o-transition: all 1.2s ease;  transition: all 1.2s ease; ' +
        'bottom:-400px; right: 60px; opacity:0; width:300px; text-align: center; background: #333333; color: #fff;">' +
        '<i style="position: absolute; font-size:20px; right:0px; cursor:pointer; top: 0px; padding:10px;" class="fa fa-times-circle bug-ui-close"></i>' +
        '<h4 style="color:#fff; margin-bottom:20px;">Submit A Bug!</h4>' +
        '<input class="bug-ui" style="padding:10px; width:280px; display:block; font-size: 1.0em; font-weight: 400; ' +
        'font-family: \'Roboto\', sans-serif;" type="text" id="bug_email" name="bug_email" value="Email or Username" />' +
        '<textarea class="bug-ui" style="padding:10px; width:280px; margin-top: 10px; font-size: 1.0em; font-weight: 400; ' +
        'font-family: \'Roboto\', sans-serif; display:block;" id="bug_report" name="bug_report">What Went Wrong?</textarea>' +
        '<button class="bug-ui-submit" style=" width:110px; background: #fff; color: #313131; font-family: \'Quicksand\', sans-serif; text-transform: uppercase; font-weight: 700; ' +
        '-webkit-transition: all 0.4s ease; padding:20px; -moz-transition: all 0.4s ease; -o-transition: all 0.4s ease;  transition: all 0.4s ease; ' +
        'font-size: 12px;  letter-spacing: 3px;  padding: 10px 20px; padding-bottom: 8px; line-height: 12px; margin: 0; margin-top: 10px; margin-left:192px; border: none;' +
        'outline: none; border-bottom: 4px solid #f12a27; cursor:pointer;"><span class="bug-ui-button-text">Submit</span></button></div>';

    $body.prepend($icon);
    $body.prepend($popup);

    $(document).on("mousedown", ".bug-ui-report", function (e) {

        triggerBugReport();

    });

    $(document).on("mouseover", ".bug-ui-report", function (e) {

        $(this).css('text-shadow', '0px 1px 2px #f12a27');

    });

    $(document).on("mouseout", ".bug-ui-report", function (e) {

        $(this).css('text-shadow', '0px 0px 2px #fff');

    });

    $(document).on("mousedown", ".bug-ui-close", function (e) {

        triggerBugReport();

    });

    $(document).on("mousedown", ".bug-ui-submit", function (e) {

         submitBug();

    });

    $(document).on("focus blur", ".bug-ui", function(event){
        if (event.type == "focusin") {
            if (this.value === this.defaultValue) {
                this.value = '';
            }
        }else{
            if (this.value === '') {
                this.value = this.defaultValue;
            }
        }
    });

    function triggerBugReport(){

        var $bug_form = $("#bugReportForm");
        var $bug_email = $("#bug_email");
        var $bug_report = $("#bug_report");

        if($bug_form.css('opacity') > 0){
            $bug_form.css('opacity', '0');
            $bug_form.css('bottom', '-300px');

            setTimeout(function(){

                var $bug_button_text = $(".bug-ui-button-text");
                var $bug_button = $(".bug-ui-submit");

                $bug_button_text.html('Submit');
                $bug_button.css('background', '#fff');
                $bug_button.css('color', '#313131');
                $bug_button.css('border-bottom', '4px solid #f12a27');

            }, 1000);

        }else{

            $bug_form.css('opacity', '1');
            $bug_form.css('bottom', '200px');

            $bug_email.val('Email');
            $bug_report.val('What Went Wrong?');
            
        }

    }

    function submitBug(){

        var $bug_email = $("#bug_email").val();
        var $bug_report = $("#bug_report").val();
        var $title = document.URL;

        var $data = {page_title: $title, email: $bug_email, report: $bug_report};

        $.when(ajaxBug($data)).done(function(data){

            doneSubmit(data);

        });
    }

    function doneSubmit(data){

        var $response = jQuery.parseJSON(data);

        var $bug_button_text = $(".bug-ui-button-text");
        var $bug_button = $(".bug-ui-submit");

        if($response.success === "true" || $response.success === true){
            $bug_button_text.text('Success');
            $bug_button.css('background', '#f12a27');
            $bug_button.css('color', '#fff');
            $bug_button.css('border-bottom', '4px solid #fff');
        }else{
            $bug_button_text.text('Error!');
            $bug_button.css('background', '#f12a27');
            $bug_button.css('color', '#fff');
            $bug_button.css('border-bottom', '4px solid #fff');
        }

        setTimeout(function(){

            triggerBugReport();

        }, 2000);

    }

    function ajaxBug($data){

        $(".bug-ui-button-text").html('<i class="fa fa-spin fa-spinner" style="padding:0px 24px; font-size:14px; line-height:12px"></i>');

        return $.ajax({
            url: document.location.origin+'/_db/listn.bug.php',
            type: 'post',
            cache: false,
            data: $data,
            async: false,
            success: function(data) {

                return data;

            },
            error: function(data){
                triggerBugReport();

                alert('An error has occurred while submitting your data');

            }
        });

    }



});