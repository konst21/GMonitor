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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function actionIndex()
    {
        return $this->redirect('/gmonitor/');
    }

    public function actionAsin()
    {
        $asin = 'B000FFR5KI';
        $parser = new \app\components\Parser();
        $product = new \app\components\Product($parser);
//        $cookies = $parser->web_page_get($product->root_url)['cookies'];
//        sleep(1);
        $url = $product->url_generate($asin);$cookies='';
        $html = $parser->web_page_get($url, $cookies)['content']; echo $html;
        $product_fields = array_keys($product->fields());
        $out = [];
        foreach ($product_fields as $key) {
            @$content = $product->get_parsed_data($key, $html);
            $out[$key] = $content;
        }
        yii::$app->debig->dump($out['customer_reviews_link']);
    }

    public function actionProxy()
    {
        $proxy = new Proxy();
        $proxy->proxy_list_by_file();
        yii::$app->debig->view($proxy->proxy_list());
    }



}
