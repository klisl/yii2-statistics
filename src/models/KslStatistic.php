<?php

namespace Klisl\Statistics\Models;

use Yii;
use yii\db\ActiveRecord;

class KslStatistic extends ActiveRecord{

//    protected $table = 'kslStatistics';

//    protected $guarded = [];

    public $start_time;
    public $stop_time;
    public $add_black_list;
    public $del_black_list;
    public $del_old;
    public $reset;


    public static function tableName()
    {
        return '{{%ksl_ip_count}}';
    }


    //проверка наличия IP в черном списке (которые не надо выводить и сохранять в БД)
    //если есть хоть одна строка, то вернет true
    public function inspection_black_list($ip){

        $check = $this
            ->find()
            ->where(['=', 'ip', $ip])
            ->andWhere(['=', 'black_list_ip', 1])
            ->all();

        if (count($check)) return true;
    }


    public function setCount($ip, $str_url, $black_list_ip = 0){
        $this->ip = $ip;
        $this->str_url = $str_url;
        $this->date_ip = time();
        $this->black_list_ip = $black_list_ip;
        $this->save();
    }



    public function getCount($condition = null, $days_ago = null){

        $sec_todey = time() - strtotime('today'); //сколько секунд прошло с начала дня

        //за сколько дней показывать по-умолчанию
//        $days_show_stat = config('statistics.days_default') -1 ;
        $days_show_stat = Yii::$app->params['statistics']['days_default'] - 1 ;
//        dd($days_show_stat);

        $days_ago = time() - (86400 * $days_show_stat) - $sec_todey;
        //В формат 2017-08-05 00:00:00 как в БД
//        $days_ago = date("Y-m-d H:i:s",$date_unix);

//        dd($days_ago);
        //Выбор диапазона между двумя датами
        if(in_array( 'date_ip',$condition)) {
//            dd($condition);
            $count_ip = $this
                ->find()
                ->where(['<', 'black_list_ip', 1])
//                ->whereBetween($condition[0], [date("Y-m-d H:i:s",$condition[1]), date("Y-m-d H:i:s",$condition[2])])
                ->andWhere(["between", $condition[0], $condition[1] , $condition[2]])
                ->orderBy('date_ip')
                ->all();

        } elseif($condition){
//dd($condition);
            $count_ip = $this
                ->find()
                ->where(['<', 'black_list_ip', 1])
                ->andWhere(['>', 'date_ip', $days_ago])
                ->andWhere(['=', 'ip', $condition['ip']])
                ->orderBy('date_ip')
                ->all();

        } else {

            $count_ip = $this
                ->find()
//                ->where('black_list_ip', '<', 1)
                ->where(['<','black_list_ip', 1])
//                ->andWhere('created_at', '>', $days_ago)
                ->andWhere(['>', 'date_ip', $days_ago])
                ->orderBy('date_ip')
                ->all();

        }
//        dd($count_ip);
        return $count_ip;
    }

    //выборка номеров IP которые в черном списке
//    public function count_black_list(){
//
//            $black_list = $this
//            ->find()
//            ->select('ip')
//            ->where(['=', 'black_list_ip', 1])
//            ->distinct() //уникальные значения
//            ->all();
//
//        //По полученному массиву IP получаем значение ячейки "comment"
//        foreach ($black_list as $key => $arr){
//            $rez = $arr->find()->where(['ip' => $arr['ip']])->limit(1)->all();
//
//            dd($rez);
//            $comment = $rez[$key]
//                ->find()
//                ->select('comment')
//                ->limit(1)
//                ->all();
//
////            dd($t->comment);
//
//
////            $black_list[$key]['comment'] = $rez->comment;
//            $black_list[$key]['comment'] = $comment[0]->comment;
//
//            dd($black_list);
//        }
////        dd($black_list);
//        return $black_list;
//    }


    public function count_black_list(){
        $black_list = (new \yii\db\Query())
            ->select('ip')
            ->from('{{%ksl_ip_count}}')
            ->where(['black_list_ip' => 1])
            ->distinct() //уникальные значения
            ->all();
        //По полученному массиву IP получаем значение ячейки "comment"
        foreach ($black_list as $key=>$arr){
            $rez = self::find()->where(['ip' => $arr['ip']])->one();
            $black_list[$key]['comment'] = $rez->comment;
        }

        return $black_list;
    }



    //Добавить в черн список
    public function set_black_list($ip, $comment=''){
//        dd($comment);
        $verify_black_list = $this->find()->where(['=', 'ip', $ip])->all();
//        dd($verify_black_list);
        //Если такой IP уже есть (коллекция не пуста)
        if(!empty($verify_black_list)){
            foreach ($verify_black_list as $str){
//                dd($str);
                $str->black_list_ip = 1;
                $str->comment = $comment;
                $res = $str->save();
            }
        } else {
            $this->ip = $ip;
            $this->str_url = '';
            $this->black_list_ip = 1;
            $this->comment = $comment;
            $res = $this->save();
        }

        $session = Yii::$app->session;


        if($res){
            $session->setFlash('success', 'IP '.$ip.' добавлен в черный список');
        } else {
            $session->setFlash('danger', 'Ошибка добавления IP в черный список');
        }
    }



    //Удаление из черного списка
    public function remove_black_list($ip){
        $res = null;

        $verify_black_list = $this->find()->where(['=', 'ip', $ip])->all();
        foreach ($verify_black_list as $str){
            $str->black_list_ip = 0;
            $str->comment = null;
            $res = $str->save();
        }

        $session = Yii::$app->session;

        if($res){
            $session->setFlash('success', 'IP '.$ip.' удален из черного списка');
        } else {

            $session->setFlash('danger', 'Ошибка удаления IP из черного списка.');
        }
    }



    //Удаление данных старше 90 дней
    public function remove_old(){

        $today = time();
        $time = $today - (86400*90);
        //Формат
        $old_time = date("Y-m-d H:i:s",$time);

        $old = $this->find()->where(['<', 'date_ip', $old_time])->all();
        foreach($old as $str){
            $str->delete();
        }
//        session()->flash('status', 'Удалено '. count($old) . ' строк.');
        $session = Yii::$app->session;
        $session->setFlash('success', 'Удалено '. count($old) . ' строк.');
    }

    /*
     * Проверка был ли такой IP в течении текущих суток (0-24)
     * Если да, то не добавляем в общий счетчик посетителей за день
     */
    public function find_ip_by_day($ip, $date){

//        $time = $date->format('Y-m-d 00:00:00');
        $time = date("Y-m-d 00:00:00",$date); //0:00 полученного дня

//        $time_now = $date->subSecond()->format('Y-m-d H:i:s');
        $time_now = $date - 1; //текущее время и день минус 1 секунда

        $res = $this->find()
            ->where(['=','ip', $ip])
//            ->whereBetween('date_ip', [$time, $time_now])
            ->andWhere(["between", "date_ip", $time , $time_now])
            ->limit(1)
            ->all();

        return $res;
    }


    /*
    * Преобразуем коллекцию к виду, где элементы с более поздней датой идут в начале
    * при этом часы/минуты/секунды в расчет не берутся
    * Используется для вывода в начале таблицы текущей даты и дальше по убыванию
    */
    public function reverse($count_ip){

//        if(!$count_ip->isEmpty()){
        if(!empty($count_ip)){
//dd();


            $array = [];
            $count = 0;
//            $first_day = $count_ip->first()->date_ip->format('Y-m-d');
            $first_day = date("Y-m-d",$count_ip[0]->date_ip);

            foreach ($count_ip as $item) {
//                $one_day = $item->date_ip->format('Y-m-d');
                $one_day = date("Y-m-d",$item->date_ip);

                if ($first_day != $one_day) {
                    $count++;
                    $first_day = $one_day;
                    $array[$count] = $item;
                } else {
                    $array[$count] = $item;
                }
            };
//            dd($array);

//            return collect($array)->reverse()->collapse();
            return $array;
        }
        return $count_ip;
    }

}