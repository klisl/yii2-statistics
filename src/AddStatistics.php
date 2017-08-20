<?php

namespace Klisl\Statistics;

use Yii;

//use yii\web\Application;
//use yii\base\Module as BaseModule;
use yii\base\Behavior;
use yii\web\Controller;


class AddStatistics extends Behavior
{

    public $actions; //для каких действий контроллера


    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION  => 'add',
        ];
    }

    public function add(){

        $action_name = $this->owner->action->id; //название текущего действия
        if(array_search($action_name, $this->actions)=== FALSE) return;

        dump('метод add');

    }

}
