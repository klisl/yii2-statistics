<?php

namespace Klisl\Statistics\Controllers;

use Yii;
use yii\web\Controller;
use Klisl\Statistics\Models\KslStatistic;
use Klisl\Statistics\StatAssetsBundle;



class StatController extends Controller
{


    public function actionIndex($condition = [], $days_ago = null, $stat_ip = false)
    {
        $session = Yii::$app->session;

//        dd('StatController метод actionIndex');
        //Если доступ разрешен только аутентифицированным пользователям
        $auth_config = Yii::$app->params['statistics']['authentication'];
//        $user = \Auth::user();
        $user = Yii::$app->user->getId();


        if ($auth_config && !$user) {

            //$auth_route = config('statistics.auth_route');
            $auth_route = Yii::$app->params['statistics']['auth_route'];
            //перенаправляем на страницу входа
            return $this->redirect(Yii::$app->urlManager->createUrl([$auth_route]));
        }


        //Проверка доступа по вводу пароля
//        $password_config = config('statistics.password');
        $password_config = Yii::$app->params['statistics']['password'];

        if ($password_config) {
//            dd(1);

            $session_stat = $session->get('ksl-statistics');

//            $session_stat = session('ksl-statistics');

            if (!$session_stat || ($session_stat !== $password_config)) {
//                return view('Views::enter');

                return $this->render('enter');
            }
        }


        $count_model = new KslStatistic(); //модель
//        dd($days_ago);
        //Получение списка статистики
        $count_ip = $count_model->getCount($condition, $days_ago);
//        dd($count_ip);
        //Преобразуем коллекцию к виду где более поздняя дата идет в начале
        $count_ip = $count_model->reverse($count_ip);
//        dd($count_ip);


		/*
		 * Устанавливаем значение полей по-умолчанию для вывода в полях формы
		 */
		$count_model->date_ip = time(); //сегодня
		$count_model->start_time = date('Y-m-01'); //первое число текущего месяца
		$count_model->stop_time = time(); //сегодня

        $black_list = $count_model->count_black_list();


        // регистрируем ресурсы:
        \Klisl\Statistics\StatAssetsBundle::register($this->view);

//        return view('Views::index',[
        return $this->render('index', [

			'count_ip'=> $count_ip, //статистика
			'stat_ip' => $stat_ip, //true если фильтр по определенному IP
            'black_list' => $black_list,
        ]);
    }





    public function actionForms(){

        $request = Yii::$app->request->post();
//        dd($request);
//        $count_model = $request->except('_token');
        $count_model = $request;

        $session = Yii::$app->session;

        /*
         * Форма входа на страницу статистики
         */
        if(isset($count_model['enter'])) {
//            $password_config = config('statistics.password');
            $password_config = Yii::$app->params['statistics']['password'];

            $password_enter = $request['password'];
//            dd($password_enter);
            if ($password_config == $password_enter) {

//                session(['ksl-statistics' => $password_config]);

                $session->set('ksl-statistics', $password_config);


//                return redirect()->route('statistics');
//                $this->redirect(\yii\helpers\Url::to(['statistics']));
                return $this->redirect(Yii::$app->urlManager->createUrl('statistics/stat/index'));

            } else {

//                return $this->redirect(Yii::$app->urlManager->createUrl('statistics/stat/enter'));
                // регистрируем ресурсы:
                \Klisl\Statistics\StatAssetsBundle::register($this->view);


                $session->setFlash('danger', 'Неверный пароль');

                return $this->render('enter');

                // регистрируем ресурсы:
//                \Klisl\Statistics\StatAssetsBundle::register($this->view);
//                session()->flash('error', 'Неверный пароль');
//                dump(333);
//                return view('Views::enter');
//                return $this->render('enter', [
//                    'error' => 'Неверный пароль',
//                ]);
//                $this->redirect(\yii\helpers\Url::to(['statistics/stat/forms']));
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
//            dd($condition);
        }


        //За период
        if(isset($count_model['period'])){

            if(isset($count_model['start_time'])){
                $timeStartUnix = strtotime($count_model['start_time']);
            } else {
                $timeStartUnix = 0;
            }

            //Если не передана дата конца - ставим текущую
            if(!isset($count_model['stop_time'])) {
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

//            if(!$count_model['ip']) session()->flash('error', 'Укажите IP для поиска');
            if(!$count_model['ip']) $session->setFlash('danger', 'Укажите IP для поиска');
        }


        //Добавить в черный список
        if(isset($count_model['add_black_list'])){

            if(!$count_model['ip']){
//                session()->flash('error', 'Укажите IP для добавления в черный список');
                $session->setFlash('danger', 'Укажите IP для добавления в черный список');
//                $error =  'Укажите IP для добавления в черный список';
            } else {
                $ip = $request['ip'];

                if(!isset($count_model['comment'])) $count_model['comment'] ='';
                $model->set_black_list($count_model['ip'], $count_model['comment']);

//                $rules = [
//                    'ip'=>'ip',
//                ];
//                $validator = \Validator::make($ip, $rules);
//                if ($validator->fails()) {
//                    session()->flash('error', 'Указан неправильный IP');
//                } else {
//                    if(!isset($count_model['comment'])) $count_model['comment'] ='';
//                    $model->set_black_list($count_model['ip'], $count_model['comment']);
//                }
            }
        }

        //Удалить из черного списка
        if(isset($count_model['del_black_list'])){

            if(!$count_model['ip']){
//                session()->flash('error', 'Укажите IP для удаления из черного списка');
                $session->setFlash('danger', 'Укажите IP для удаления из черного списка');
//                $error =  'Укажите IP для удаления из черного списка';
            } else {
                $model->remove_black_list($count_model['ip']);
            }
        }

        //Удалить старые данные
        if(isset($count_model['del_old'])){
            $model->remove_old();
        }
//        dd(1);

//        return $this->actionIndex($condition, $days_ago, $stat_ip);
//        return $this->actionIndex($condition, $days_ago, $stat_ip);
        return $this->redirect(Yii::$app->urlManager->createUrl('statistics/stat/index',array($condition,$days_ago,$stat_ip)));
    }


//    public function enter(){
//        $request = Yii::$app->request->post();
////        $password_config = config('statistics.password');
//        $password_config = Yii::$app->params['statistics']['password'];
//
//        $password_enter = $request['password'];
//
//        if($password_config == $password_enter){
//
////            session(['ksl-statistics' => $password_config]);
//            $session = Yii::$app->session;
//            $session->set('ksl-statistics', $password_config);
//
////            return redirect()->route('statistics');
//            return $this->redirect(Yii::$app->urlManager->createUrl('statistics/stat/index'));
//
//        } else {
//
//            // регистрируем ресурсы:
//            \Klisl\Statistics\StatAssetsBundle::register($this->view);
//
//            return $this->render('enter', [
//                'error' => 'Неверный пароль',
//            ]);
//        }
//
//    }
}
