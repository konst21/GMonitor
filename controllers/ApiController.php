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
     * status of all funtions in Gearman
     * @return string JSON - array, format view in function PHPDoc
     */
    public function actionFunction_status ()
    {
        $functions = Yii::$app->gmonitor->all_functions_statuses();
        return json_encode($functions);
    }

    /**
     * quantity of all workers
     * @return int count of workers
     */
    public function actionWorker_count ()
    {
        return Yii::$app->gmonitor->worker_count();
    }

    /**
     * optionally add GET paramater "?count=<count of workers>", default 1
     * @return string
     */
    public function actionWorker_start ()
    {
        $count = Yii::$app->request->get('count')?Yii::$app->request->get('count'):1;
        $worker_name = Yii::$app->request->get('name')?Yii::$app->request->get('name'):'main';
        for ($i=0; $i<$count; $i++) {
            Yii::$app->gmonitor->worker_start($worker_name);
        }
        return 'ok';
    }

    /**
     * Stop all workers
     * @return string
     */
    public function actionWorker_stop ()
    {
        $worker_name = Yii::$app->request->get('name')?Yii::$app->request->get('name'):'main';
        Yii::$app->gmonitor->workers_stop($worker_name);
        return 'ok';
    }

    /**
     * Reset queue for one function
     */
    public function actionFunction_queue_reset ()
    {
        if (!Yii::$app->request->get('function_name')) {
            return 'GET parameter "function_name" needed';
        }
        $function_name = Yii::$app->request->get('function_name');
        $functions_list = Yii::$app->gmonitor->all_functions_statuses();
        if (isset($functions_list['data'][$function_name]) && $functions_list['data'][$function_name]['in_queue'] > 0) {
            Yii::$app->gmonitor->reset_function_queue($function_name);
            return 'ok';
        }
        return "ok";
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