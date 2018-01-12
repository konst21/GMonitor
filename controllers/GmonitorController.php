<?php
namespace app\controllers;

use app\components\Brand;
use app\components\GMonitor;
use app\components\Parser;
use Yii;
use yii\web\Controller;
use yii\web\Response;
class GmonitorController extends Controller
{
    public $layout = 'gmain';

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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionIndex ()
    {
        return $this->render('index', [
            'workers_count' => GMonitor::worker_count(),
            'js_templates' => $this->renderPartial('js_templates'),
        ]);
    }

    public function actionProduct ()
    {
        $db = new \app\models\Product();
        $products = $db->select_global()?$db->select_global():[];
        return $this->render('product', ['products' => $products]);
    }

    public function actionBrand ()
    {
        $db = new \app\models\Brand();
        $fields = array_keys($db->fields_mapping());
        $brand = $db->select_global()?$db->select_global():[];
        return $this->render('brand', ['brand' => $brand, 'fields' => $fields]);
    }

    public function actionReset()
    {
        $db = new \app\models\Product();
        $db->clear_db();
        $db2 = new \app\models\Brand();
        $db2->clear_db();
        return $this->redirect('/gmonitor/');
    }

    public function actionAsinfile()
    {
        if (isset($_FILES['plist'])) {
            $list = file_get_contents($_FILES['plist']['tmp_name']);
            $list = explode(PHP_EOL, $list);
            $client = new \GearmanClient();
            $client->addServer('localhost');
            foreach ($list as $asin) {
                $asin = trim($asin);
                if (strlen($asin) > 5) {
                    $gearman_data = [
                        'asin' => trim($asin)
                    ];
                    $client->doBackground('product', serialize($gearman_data));
                }
            }
        }
        return $this->redirect('/gmonitor/');
    }

    public function actionBrandfile()
    {
        if (isset($_FILES['plist'])) {
            $list = file_get_contents($_FILES['plist']['tmp_name']);
            $list = explode(PHP_EOL, $list);
            $client = new \GearmanClient();
            $client->addServer('localhost');
            foreach ($list as $seller) {
                $seller = trim($seller);
                if (strlen($seller) > 5) {
                    $gearman_data = [
                        'seller' => trim($seller),
                        'marketplaceID' => 'A1PA6795UKMFR9',
                    ];
                    $client->doBackground('brand', serialize($gearman_data));
                }
            }
        }
        return $this->redirect('/gmonitor/');
    }

    public function actionAsincollect()
    {
        if (isset($_FILES['plist'])) {
            $list = file_get_contents($_FILES['plist']['tmp_name']);
            $list = explode(PHP_EOL, $list);
            $client = new \GearmanClient();
            $client->addServer('localhost');
            foreach ($list as $seller) {
                $seller = trim($seller);
                if (strlen($seller) > 5) {
                    $gearman_data = [
                        'merchant' => trim($seller),
                        'page' => 1,
                    ];
                    $client->doBackground('asin_collect', serialize($gearman_data));
                }
            }
        }
        return $this->redirect('/gmonitor/');
    }

    public function actionTestq ()
    {
        $html = file_get_contents('/var/www/slyii.nottes.net/brand.html');
        $parser = new \app\components\Parser();
        $brand = new \app\components\Brand($parser);
        $brand->parser->xpath_create($html);
        $z = $brand->get_parsed_data('review_block');
        //yii::$app->debig->view($z);
        echo $z;
    }

    public function actionTt ()
    {
        echo intval(preg_replace('/[^\d]/', '', '52&nbsp;new'));
    }



    public function actionInit()
    {
        $brand = new \app\models\Brand();
        $brand->create_db_mapping_index();
        $brand->clear_db();
        echo 'ok';
    }

    public function actionCstat()
    {
        $proxy = new \app\models\Proxy();
        $list = $proxy->country_stats();
        //yii::$app->debig->view($list);
        return $this->render('proxy_country', ['list' => $list]);
    }

    public function actionProxy()
    {
        $proxy = new \app\models\Proxy();
        $list = $proxy->all_proxy_data();
        //yii::$app->debig->view($list);
        return $this->render('proxy_stat', ['proxy' => $list]);
    }

    public function actionTestbl()
    {
        $parser = new Parser();
        $asin = new \app\components\Asin($parser);
        $uniq_parameters = [
            'merchant' => 'AZCCDXR4O3ZZ',
            'page' => 1,
        ];
        $url = $asin->url_generate($uniq_parameters);
        $html = $asin->parser->web_page_get($url)['content'];
        $asin->parser->xpath_create($html);

        $block = $asin->parser->html('//*[@id="resultsCol"]');
        echo count(explode('data-asin', $block));
        preg_match_all('/data-asin\=\"[^\"]{10,20}\"/', $block, $z);
        yii::$app->debig->view($z[0]);
    }

    public function actionAscollect()
    {
        $asin = new \app\models\Asin('AZCCDXR4O3ZZ');
        yii::$app->debig->view(array_keys($asin->all_asins()));
    }

}
