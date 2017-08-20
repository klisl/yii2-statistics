<?php

namespace Klisl\Statistics;

use Yii;
//use yii\base\BootstrapInterface;
use yii\web\Application;
use yii\base\Module as BaseModule;
use yii\base\Behavior;



class Module extends BaseModule //implements BootstrapInterface
{
//    public $id;
//    public $actions; //для каких действий контроллера

    public $controllerNamespace = 'Klisl\Statistics\Controllers';





//    //Метод, который вызывается автоматически при каждом запросе
//    public function bootstrap($app)
//{
//
//    $app->on(Application::EVENT_BEFORE_REQUEST, function () {
////        $this->start();
////        dd(Yii::$app->urlManager);
//
//
//        //Контроллер
//        Yii::$app->controllerMap = ['statistics' => 'Klisl\Statistics\Controllers\StatController'];
//        //Правило маршрутизации
//        Yii::$app->urlManager->addRules(['statistics'=>'statistics/stat/index']);
//
//    });
//
//}

//    public function init()
//    {
////        $this->module = Yii::$app->getModule('statistics');
//        parent::init();
//    }


//    public function start(){
//        dd('start');
//    }

//    public function events()
//    {
//        return [
//            Controller::EVENT_BEFORE_ACTION  => 'add',
//        ];
//    }
//
//    public function add(){
//
//        $action_name = $this->owner->action->id; //название текущего действия
//        if(array_search($action_name, $this->actions)=== FALSE) return;
//
//
//        dump('метод add');
//
//    }

//    public function getUrlRules()
//    {
//        return array(
//            'statistics'=>'statistics/stat/index',
//        );
//    }
}
