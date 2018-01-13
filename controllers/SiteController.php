<?php

namespace app\controllers;

use app\components\Debig;
use app\components\Parser;
use app\components\Product;
use app\models\Esbase;
use app\models\Proxy;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;


class SiteController extends Controller
{
    public $layout = 'redirect';

    /**
     *
     * @return string
     */

    public function actionIndex()
    {
        return $this->redirect('/gmonitor/');
    }

}
