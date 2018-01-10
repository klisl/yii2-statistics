<?php

namespace Klisl\Statistics;


use yii\base\BootstrapInterface;


/**
 * Предзагрузка расширения
 *
 * @package Klisl\Statistics
 */
class Bootstrap implements BootstrapInterface{

    /**
     * Метод, который вызывается автоматически при каждом запросе
     *
     * @param \yii\base\Application $app
     * @return void
     */
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