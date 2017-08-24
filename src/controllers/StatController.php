<?php

namespace Klisl\Statistics\controllers;

use Yii;
use yii\web\Controller;
use Klisl\Statistics\models\KslStatistic;



class StatController extends Controller
{

    public $layout = 'main';

    public function actionIndex($condition = [], $days_ago = null, $stat_ip = false)
    {
        // регистрируем ресурсы:
        \Klisl\Statistics\StatAssetsBundle::register($this->view);

        $session = Yii::$app->session;

        //Если доступ разрешен только аутентифицированным пользователям
        $auth_config = KslStatistic::getParameters()['authentication'];

        $user = Yii::$app->user->getId();


        if ($auth_config && !$user) {

            $auth_route = KslStatistic::getParameters()['auth_route'];

            //перенаправляем на страницу авторизации
            if($auth_route){
                return $this->redirect(Yii::$app->urlManager->createUrl([$auth_route]));
            }
            else {
                Yii::$app->user->loginRequired(); //на стандартную страницу авторизации
            }

        }


        //Проверка доступа по вводу пароля
        $password_config = KslStatistic::getParameters()['password'];

        if ($password_config) {

            $session_stat = $session->get('ksl-statistics');

            if (!$session_stat || ($session_stat !== $password_config)) {

                return $this->render('enter');
            }
        }


        $count_model = new KslStatistic(); //модель
        //Получение списка статистики
        $count_ip = $count_model->getCount($condition, $days_ago);
        //Преобразуем коллекцию к виду где более поздняя дата идет в начале
        $count_ip = $count_model->reverse($count_ip);


		/*
		 * Устанавливаем значение полей по-умолчанию для вывода в полях формы
		 */
		$count_model->date_ip = time(); //сегодня
		$count_model->start_time = date('Y-m-01'); //первое число текущего месяца
		$count_model->stop_time = time(); //сегодня

        $black_list = $count_model->count_black_list();



        return $this->render('index', [
			'count_ip'=> $count_ip, //статистика
			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
            'black_list' => $black_list,
        ]);
    }





    public function actionForms(){

        $request = Yii::$app->request->post();

        $count_model = $request;

        $session = Yii::$app->session;

        /*
         * Форма входа на страницу статистики
         */
        if(isset($count_model['enter'])) {

            $password_config = KslStatistic::getParameters()['password'];

            $password_enter = $request['password'];

            if ($password_config == $password_enter) {

                $session->set('ksl-statistics', $password_config);

                return $this->redirect(Yii::$app->urlManager->createUrl('statistics/stat/index'));

            } else {

                // регистрируем ресурсы:
                \Klisl\Statistics\StatAssetsBundle::register($this->view);

                $session->setFlash('danger', 'Неверный пароль');

                return $this->render('enter');
            }
        }


        /*
         * Формы выбора параметров вывода статистики
         */
        $condition = [];
		$days_ago = null;
        $stat_ip = false;

        $model = new KslStatistic();


        //Сброс фильтров
        if(isset($count_model['reset'])){
            $condition = [];
        }

        if(isset($count_model['date_ip'])){
            $time = strtotime($count_model['date_ip']);
            $time_max = $time + 86400;
            $condition = ["date_ip", $time , $time_max];
        }


        //За период
        if(isset($count_model['period'])){

            if(!empty($count_model['start_time'])){
                $timeStartUnix = strtotime($count_model['start_time']);
            } else {
                $timeStartUnix = 0;
            }

            //Если не передана дата конца - ставим текущую
            if(empty($count_model['stop_time'])) {
                $timeStopUnix = time();
            } else {
                $timeStopUnix = strtotime($count_model['stop_time']);
            }

            $timeStopUnix += 86400; //целый день (до конца суток)
            $condition = ["date_ip", $timeStartUnix , $timeStopUnix];
        }


        //По IP
        if(isset($count_model['search_ip'])){

            $condition = ["ip" => $count_model['ip']];
            $stat_ip = true;

            if(!$count_model['ip']) $session->setFlash('danger', 'Укажите IP для поиска');
        }


        //Добавить в черный список
        if(isset($count_model['add_black_list'])){

            if(!$count_model['ip']){

                $session->setFlash('danger', 'Укажите IP для добавления в черный список');

            } else {

                if(!isset($count_model['comment'])) $count_model['comment'] ='';
                $model->set_black_list($count_model['ip'], $count_model['comment']);

            }
        }

        //Удалить из черного списка
        if(isset($count_model['del_black_list'])){

            if(!$count_model['ip']){

                $session->setFlash('danger', 'Укажите IP для удаления из черного списка');

            } else {
                $model->remove_black_list($count_model['ip']);
            }
        }

        //Удалить старые данные
        if(isset($count_model['del_old'])){
            $model->remove_old();
        }

        return $this->actionIndex($condition, $days_ago, $stat_ip);
    }

}
