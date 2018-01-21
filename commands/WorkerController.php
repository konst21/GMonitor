<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\GMonitor;
use yii\console\Controller;
use yii;

require_once 'functions.php';

/**
 * worker
 *
 */
class WorkerController extends Controller
{

    /**
     * main worker - parser
     */
    public function actionMain ()
    {
        $worker = new \GearmanWorker();
        $worker->addServer(\Yii::$app->gmonitor->host, \Yii::$app->gmonitor->port);
        foreach (GMonitor::functions_list() as $func) {
            if (is_callable($func)) {
                $worker->addFunction($func, $func);
            }
        }
        while ($worker->work()){}
    }

    /**
     * fake worker - reset function's queue
     * @param $function_name
     */
    public function actionFake ($function_name)
    {
        eval('function ' . $function_name . '_fake(){};');
        $worker = new \GearmanWorker();
        $worker->addServer(Yii::$app->gmonitor->host, Yii::$app->gmonitor->port);
        $worker->addFunction($function_name, $function_name . '_fake');
        while($worker->work()){
            if (!yii::$app->gmonitor->function_status($function_name)) die();
        }
    }

}
