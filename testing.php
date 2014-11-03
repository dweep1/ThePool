<?php

    date_default_timezone_set('America/New_York');

    if (date('I', time()))    {
        echo 'We\'re in DST!';
    }    else    {
        echo 'We\'re not in DST!';
    }