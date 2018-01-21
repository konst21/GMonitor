<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class ApiController extends Controller
{
    /**
     * @var string
     */
    public $layout = 'api';

    /**
     * @return string JSON
     */
    public function actionFunction_status ()
    {
        $functions = Yii::$app->gmonitor->all_functions_statuses();
        return json_encode($functions);
    }

    /**
     * @return int count of workers
     */
    public function actionWorker_count ()
    {
        return Yii::$app->gmonitor->worker_count();
    }

    /**
     * start needed count of workers
     * @return string
     */
    public function actionWorker_start ()
    {
        $count = Yii::$app->request->get('count')?Yii::$app->request->get('count'):1;
        for ($i=0; $i<$count; $i++) {
            Yii::$app->gmonitor->main_worker_start();
        }
        return 'ok';
    }

    /**
     * @return string
     */
    public function actionWorker_stop ()
    {
        Yii::$app->gmonitor->main_worker_stop();
        return 'ok';
    }

    /**
     * Reset queue for one function
     */
    public function actionReset_function_queue ()
    {
        $function_name = Yii::$app->request->get('function_name');
        $functions_list = Yii::$app->gmonitor->all_functions_statuses();
        if (isset($functions_list['data'][$function_name]) && $functions_list['data'][$function_name]['in_queue'] > 0) {
            Yii::$app->gmonitor->reset_function_queue($function_name);
            return 'ok';
        }
        return "empty";
    }

    /**
     * Reset queue for all functions in Gearman Job Server
     */
    public function actionReset_all_queue ()
    {
        //@todo check reset each function
        Yii::$app->gmonitor->reset_all_queue();
        return 'ok';
    }
}