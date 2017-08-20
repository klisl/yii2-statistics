<?php

namespace Klisl\Statistics;

use Yii;
use yii\base\BootstrapInterface;
//use yii\web\Application;



class Bootstrap implements BootstrapInterface{

    //Метод, который вызывается автоматически при каждом запросе
    public function bootstrap($app)
    {
        //Правила маршрутизации
        $app->getUrlManager()->addRules([
            'statistics' => 'statistics/stat/index',
            'statistics/stat/forms' => 'statistics/stat/forms',
        ], false);

        /*
         * Регистрация модуля в приложении
         * (вместо указания в файле frontend/config/main.php
         *  'modules' => [
         *      'statistics' => 'Klisl\Statistics\Module'
         *  ],
         */
         $app->setModule('statistics', 'Klisl\Statistics\Module');


//        yii migrate --migrationPath=@Klisl/Statistics/migrations --interactive=0

//        yii migrate --migrationPath=@vendor/klisl/yii2-statistics/src/migrations


//        Yii::setAlias('@stat', '@vendor/klisl/yii2-statistics');

//        dd($app->controllerMap['migrate']);
//        $app->controllerMap['migrate'] = [
//
//                'class' => 'yii\console\controllers\MigrateController',
//                'migrationNamespaces' => [
////                    'app\migrations', // Общие миграции приложения
////                    'module\migrations', // Миграции одного из модулей проекта
//                    'Klisl\Statistics\Migrations', // Миграции одного из расширений
//                ],
//
//
//        ];




//        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
//
//        });

    }
}