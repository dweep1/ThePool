
<!--
<div id="debug">
    <?php

        global $time;
        global $mem;
        global $memTwo;

        $memTemp = number_format((($memTwo-$mem) / 1024), 2);
        $memTwoTemp = number_format(((memory_get_usage() - ($memTwo-$mem)) / 1024), 2);
        $memThreeTemp = number_format(((memory_get_usage() - $mem) / 1024), 2);
        $timeTemp =  number_format((microtime(TRUE) - $time), 6);

        echo "Head : $memTemp KB<br/>";
        echo "Page : $memTwoTemp KB <br/>";
        echo "Rend : $memThreeTemp KB <br/>";
        echo "Time: $timeTemp sec";

    ?>
</div>
-->

