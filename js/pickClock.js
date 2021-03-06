
function pickCount(){

    clockAjax({submitType: 0}, function(data){

        $("#pickHold").html(data);

    });

}

function lockTimer(){

    var jan = new Date( 2009, 0, 1, 2, 0, 0 ), jul = new Date( 2009, 6, 1, 2, 0, 0 );
    var offset = (jan.getTime() % 24 * 60 * 60 * 1000) > (jul.getTime() % 24 * 60 * 60 * 1000) ? jan.getTimezoneOffset() : jul.getTimezoneOffset();

    clockAjax({submitType: 1, offset: offset}, function(data){

        if(data == false || data == "false"){
            $('#lockHold').empty();
            $('#lockHold').html('End of Season');
        }else{
            var d = Date.createFromMysql(data);
            var $lockHold = $('#lockHold');
            var $dayLock = 'Thursday';

            if(d.getDay() == 5){
                $dayLock = 'Friday';
            }else if(d.getDay() == 6){
                $dayLock = 'Saturday';
            }else if(d.getDay() == 0){
                $dayLock = 'Sunday/Monday';
            }else if(d.getDay() <= 1){
                $dayLock = 'Sunday/Monday';
            }

            $('#day').html($dayLock);

            $lockHold.css({'width': '200px'});
            setTimeout(function(){
                $lockHold.css({'width': 'auto'});
            }, 20);//end timeout

            $('#lockClock').tinyTimer({ to: d, format: '%d Days, %0h:%0m:%0s',  onEnd: function(){
                $('#lockHold').empty();
                $('#lockHold').html($dayLock+'\'s Picks Locked');

                setTimeout(function(){
                    if (typeof endClock == 'function') {
                        endClock();
                    }
                }, 1000);

            }});//end of tinytimer
        }

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