<?php

namespace Klisl\Statistics;

use Yii;
use yii\base\BootstrapInterface;



class Bootstrap implements BootstrapInterface{

    //Метод, который вызывается автоматически при каждом запросе
    public function bootstrap($app)
    {

        //Правила маршрутизации
        $app->getUrlManager()->addRules([
            'statistics' => 'statistics/stat/index',
            'statistics/forms' => 'statistics/stat/forms',
        ], false);

        /*
         * Регистрация модуля в приложении
         * (вместо указания в файле frontend/config/main.php
         *  'modules' => [
         *      'statistics' => 'Klisl\Statistics\Module'
         *  ],
         */
         $app->setModule('statistics', 'Klisl\Statistics\Module');


    }
}