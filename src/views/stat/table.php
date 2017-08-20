<?php

    $now_date = ''; //текущая дата которая выводится в таблице
    $show_new_date = false; //показать смену даты в таблице
    $transition = 1; //счетчик переходов на страницы
    $count = 0; //общий счетчик посетителей за период
    $count_day = 0; //счетчик посетителей за 1 день
    $num_ip = ''; //хранение текущего IP
    $old = 0; //кол-во найденных строк с IP за данный день

    //Получение первой даты по которой отображать статистику
    if (isset($count_ip[0])){
//        $date = $count_ip[0]->date_ip->format('d.m.Y');

        $date = date("Y-m-d",$count_ip[0]->date_ip);
//        dd($date);
    } else $date = date("d.m.Y",time()); //Если дата не установлена, то выводим за сегодняшний день
?>

    <?php if (!$stat_ip) { ?>
        <h5>Количество уникальных посетителей по дням:</h5>
    <?php } else { ?>
        <h5>Количество посещений пользователя с указанным IP:</h5>
    <?php } ?>




<table class='get_table'>
    <thead>
     <tr>
        <th>Переходы на страницы сайта</th>
        <th>IP</th>
        <th>URL просматриваемой страницы</th>
        <th>Время посещения</th>
    </tr>
    </thead>
    <tbody>

	<?php foreach ($count_ip as $key => $value){


        //кол-во посетителей по дням (вывод последнего дня после цикла)
//        if($date != $value->date_ip->format('d.m.Y')) {
        if($date != date("d.m.Y",$value->date_ip)) {
            echo $date . ' - '. $count_day . '<br>';
//            $date = $value->date_ip->format('d.m.Y');
            $date = date("d.m.Y",$value->date_ip);
            $count_day = 0;
        }

        if ($stat_ip) $count_day++; //для фильтра по определенному IP


        //Если сменился IP или дата, то включаем счетчики
//        if (($num_ip != $value->ip) || ($now_date !=$value->date_ip->format('Y-m-d'))){
        if (($num_ip != $value->ip) || ($now_date !=date("Y-m-d",$value->date_ip))){

            $num_ip = $value->ip; //сохраняем текущий IP

//            if($now_date !=$value->date_ip->format('Y-m-d')){
            if($now_date !=date("Y-m-d",$value->date_ip)){

//                $now_date = $value->date_ip->format('Y-m-d');
                $now_date = date("Y-m-d",$value->date_ip); //сохраняем текущую дату

                $show_new_date = true;
            } else $show_new_date = false;


            $transition = 1;

            /*
             * тут проверка был ли такой IP в течении текущих суток (0-24)
             * Если да, то не добавляем в общий счетчик посетителей за день
             */
            $find = $value->find_ip_by_day($value->ip, $value->date_ip);

            //Если такого IP еще не было в этот день
            if(empty($find)){
                $count++;
                if (!$stat_ip) $count_day++; //для фильтра по определенному IP
                $old = 0;
            } else {
                $old = $find->count();
            }


        } else {
            $transition++;
        }

        echo "<tr ";


        if ($transition == 1 ) {

            if ($show_new_date && !$old) {
//                echo "class='tr_first red'><td colspan='4'>{$value->date_ip->format('d.m.Y')}</td></tr>";
                echo "class='tr_first red'><td colspan='4'>{date(\"d.m.Y\",$value->date_ip)}</td></tr>";
                echo "<tr class='tr_first'><td colspan='4'>НОВЫЙ ПОСЕТИТЕЛЬ</td></tr>";

            }
            else if ($show_new_date && $old) {
//                echo "class='tr_first red'><td colspan='4'>{$value->date_ip->format('d.m.Y')}</td></tr>";
                echo "class='tr_first red'><td colspan='4'>{date(\"d.m.Y\",$value->date_ip)}</td></tr>";
                echo "<tr class='tr_first'><td colspan='4'>уже был</td></tr>";
            }
            elseif ($old) {
                echo "class='tr_first'><td colspan='4'>уже был</td></tr>";

            } else {
                echo "class='tr_first'><td colspan='4'>НОВЫЙ ПОСЕТИТЕЛЬ</td></tr>";
            }
        }

        else {
           echo ">";
        }
        echo "<td>$transition</td>
            <td><a href='http://speed-tester.info/ip_location.php?ip=".$value->ip."' target=\"_blank\">".$value->ip."</a></td>  	
            <td><a href='".$value->str_url."' target=\"_blank\">".$value->str_url."</a></td>                     
            <td>".date('d.m.Y H:i:s',$value->date_ip)."</td></tr>";


    }

        //вывод кол-ва посетителей за последнее число
        if(isset($value)){
//            $date = $value->date_ip->format('d.m.Y');
            $date = date("d.m.Y",$value->date_ip);
            if($date) echo $date . ' - '. $count_day . '<br>';
        }
	?>

        <p>Всего посетителей за период - <?=$count?></p>
    </tbody>
</table>