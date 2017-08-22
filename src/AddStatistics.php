<?php

namespace Klisl\Statistics;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use Klisl\Statistics\Models\KslStatistic;


/*
 * Активирует счетчик статистики по событию EVENT_BEFORE_ACTION
 * для указанных действий контроллера в методе behaviors()
 */
class AddStatistics extends Behavior
{

    public $actions; //для каких действий контроллера


    public function events()
    {
        return [
            Controller::EVENT_AFTER_ACTION  => 'add',
        ];
    }


    public function add(){

        $action_name = $this->owner->action->id; //название текущего действия
        if(array_search($action_name, $this->actions)=== FALSE) return;

        $ip = Yii::$app->request->userIP; //получаем IP текущего посетителя
//        if($ip == '127.0.0.1') return;

        $count_model = new KslStatistic(); //модель


        $str_url =  "http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]; //URL текущей страницы c параметрами

        //Проверка на бота
        $bot_name = self::isBot();
        //$bot_name = 'rambler'; //для тестирования

        if(!$bot_name){
            //Проверка в черном списке
            $black = $count_model->inspection_black_list($ip);
            if(!$black){
                $count_model->setCount($ip, $str_url, 0);
            }
        }
    }


    //Проверяет, является ли посетитель роботом поисковой системы из перечня.
    public static function isBot(&$botname = ''){
        $bots = array(
            'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
            'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
            'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
            'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
            'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
            'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
            'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
            'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
            'Nigma.ru','bing.com','dotnetdotcom'
        );
        foreach($bots as $bot)
            if(stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false){
                $botname = $bot;
                return $botname;
            }
        return false;
    }

}
