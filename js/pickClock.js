
function pickCount(){

    clockAjax({submitType: 0}, function(data){

        $("#pickHold").html(data);

    });

}

function lockTimer(){

    var $today = new Date();

    clockAjax({submitType: 1, offset: $today.getTimezoneOffset()}, function(data){

        var d = Date.createFromMysql(data);
        var $lockHold = $('#lockHold');
        var $dayLock = 'Thurs';

        if(d > $today && d.getDay() == 5){
            $dayLock = 'Thurs';
        }else if(d > $today && d.getDay() == 0){
            $dayLock = 'Sun/Mon';
        }else if($today.getDay() == 6){
            $dayLock = 'Sun/Mon';
        }else if($today.getDay() <= 1){
            $dayLock = 'Sun/Mon';
        }

        $('#day').html($dayLock);

        $lockHold.css({'width': '200px'});
        setTimeout(function(){
            $lockHold.css({'width': 'auto'});
        }, 20);//end timeout

        $('#lockClock').tinyTimer({ to: d, format: '%d Days, %0h:%0m:%0s',  onEnd: function(){
            $('#lockHold').empty();
            $('#lockHold').html($dayLock+'\'s Picks Locked');
        }});//end of tinytimer

    });

}

Date.createFromMysql = function(mysql_string){
    if(typeof mysql_string === 'string')
    {
        var t = mysql_string.split(/[- :]/);

        //when t[3], t[4] and t[5] are missing they defaults to zero
        return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
    }

    return null;
};

function clockAjax($data, $callback){

    return $.ajax({
        url: './_listeners/listn.clock.php',
        type: 'post',
        cache: false,
        data: $data,
        async: false,
        success: function(data) {

            if (typeof $callback == 'function') {
                $callback(data);
            }

            return $data;

        }
    });

}