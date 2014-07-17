var $dateTimeCheck = true;

$(document).ready(function(){

    modernizrCheck();
    dateTimeFormat();

    $(window).resize(popupResize);

});

function modernizrCheck(){

    Modernizr.load({
        test: Modernizr.inputtypes.date,
        nope: ['http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css'],
        complete: function(){
            $dateTimeCheck = false;
        }
    });

}

function dateTimeFormat(){

    if($dateTimeCheck === false){
        var $dateTime = $( "input[type='date']" );
        $dateTime.addClass('ui-date-time');
        $dateTime.datepicker({ dateFormat: 'yy-mm-dd'});
    }

}

function checkSet($obj){

    if(typeof($obj) === 'undefined'){return false;}
    else if($obj === ""){return false;}
    else if($obj === " "){return false;}
    else if($obj === null){return false;}
    else if($obj === false){return false;}

    return true;

}

function disabledEventPropagation(event){

    if (event.stopPropagation){

        event.stopPropagation();

    }

}

function displayData($projectID, $url, $className){

    if(checkSet($projectID) !== true){
        $projectID = 0;
    }

    if(checkSet($className) !== true){
        $("#data-list").load( $url+"?id="+$projectID );
    }else{
        $("#data-list").load( $url+"?id="+$projectID+"&className="+$className  );
    }

}


function displayPopup($projectID, $url, $className){

    if(checkSet($projectID) !== true){
        $projectID = 0;
    }

    if(checkSet($className) !== true){
        $url = $url+"?id="+$projectID;
    }else{
        $url = $url+"?id="+$projectID+"&className="+$className;
    }


    $("#loadedContent").load($url, function(){

        setTimeout(function(){

            $('body').css('overflow', 'hidden');

            var $body;

            if($(window).outerHeight() >= $(document).outerHeight()){
                $body = $(window);
            }else{
                $body = $(document);
            }

            $('#background').css({'opacity': '1', 'min-height': $body.outerHeight()});

            popupResize();

        }, 200);

    } );

}

function popupClose(){

    var $popup = $('#background');

    $popup.css({'opacity': '0'});

    $('body').css('overflow', 'visible');

    setTimeout(function(){
        $popup.remove();

        $popup = $();
    }, 700);

}

function popupResize(){

    var $overlay =  $('#overlay');
    var $body = 0;

    if($(window).outerHeight() > 100){
        $body = $(window);
    }else{
        $body = $(document);
    }

    var $width = ($body.outerWidth()/2) - ($overlay.outerWidth()/2);
    var $height = ($body.outerHeight()/2) - ($overlay.outerHeight()/2);

    if($height <= 0){
        $height = 0;
    }

    if($width <= 0){
        $width = 0;
    }

    $overlay.css({'top': $height, 'left' : $width});

}

function popupBGResize($time){

    var $bg = $("#background");
    var $height = $(document).outerHeight();

    setTimeout(function(){
        if($bg.size() > 0){
            popupBGResize($time);
        }
    }, $time);

    if($bg.css('height') !== $height){

        popupResize();

        $bg.css({'height': $height});

    }
}

function ajaxSubmit($data){

    return $.ajax({
        url: './admin-listener.php',
        type: 'post',
        cache: false,
        data: $data,
        async: false,
        success: function(data) {

            return data;

        }
    });

}

$(document).resize(function(){
    popupResize();
});

$(document).on("mousedown", "[data-link]", function (e) {

    disabledEventPropagation(e);

    if( e.which === 2 ) {

        var productLink = $('<a href="' + $(this).attr('data-link') + '" />');

        productLink.attr("target", "_blank");
        window.open(productLink.attr("href"));

        return false;

    } else if(e.which === 1 ) {
        window.location.href = $(this).attr('data-link');
    }

});

$(document).on("mouseover", "[data-help]", function (e) {

    var $helpBox = $(".help");

    var $top = $(this).position().top;

    $helpBox.css("opacity", "1");

    $helpBox.css("top", $top+"px");

    $helpBox.html($(this).attr('data-help'));

});

$(document).on("mousedown", "[data-edit-id]", function (e) {

    disabledEventPropagation(e);

    displayPopup($(this).attr('data-edit-id'), "manage.pop.php", $(this).attr('data-object'));

});


$(document).on("mousedown", "[data-id]", function (e) {

    disabledEventPropagation(e);

    displayData($(this).attr('data-id'), "manage.data.php", $(this).attr('data-object'));

});

$(document).on("mousedown", "#new-item", function (e) {

    disabledEventPropagation(e);

    displayPopup(0, "manage.pop.php", $(this).attr('data-object'));

});

$(document).on("mousedown", "#closePopup", function (e) {

    disabledEventPropagation(e);

    popupClose();

});



function getObjects($scope, $http) {

    $scope.url = "admin.json.php";

    // Create the http post request
    // the data holds the keywords
    // The request is a JSON request.
    $http.post($scope.url, { "data" : $scope.keywords}).
        success(function(data, status) {
            $scope.status = status;
            $scope.data = data;

            if (typeof formatData == 'function') {
                formatData($scope);
            }

        })
        .
        error(function(data, status) {
            $scope.data = data || "Request failed";
            $scope.status = status;
        });

}

function displayFieldMessage($message, $timer){

    if(checkSet($timer) === false){ $timer = 6000;}

    var div = $('<div class="fieldmessage"></div>');

    div.html($message);
    var $fieldBody = $('#fieldbody');
    $fieldBody.append(div);

    var $size = 0-($fieldBody.outerHeight());

    div.css({"top": "0"});

    setTimeout(function(){

        div.css({"top": $size});

        setTimeout(function(){

            div.remove();

        }, 1300);

    }, $timer);

}