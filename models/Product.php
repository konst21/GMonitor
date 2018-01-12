<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
namespace app\models;

use app\components\Debig;
use yii;

class Product extends Esbase
{
    public function __construct()
    {
        $this->index = 'products';
        $this->type = 'product';
        $this->fields_mapping = $this->fields_mapping();
        parent::__construct();
    }

    public function fields_mapping ()
    {
        return [
            'asin' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'url' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'time_stamp' =>
                [
                    'type' => 'integer',
                ],
            'title' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'images' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'review_stars' =>
                [
                    'type' => 'float',
                ],
            'review_qty' =>
                [
                    'type' => 'integer',
                ],
            'questions' =>
                [
                    'type' => 'integer',
                ],
            'manufacturer' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'brand_href' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            /*!*/'bestseller_cat' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'price' =>
                [
                    'type' => 'float',
                ],
            'availability' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],

            //'estimate_delivery_time' => '',//не нашел такого блока
            'seller_shipper' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'qty_offers' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'bullet_points' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'product_description' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'technical_informations' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],

            'additional_product_informations' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'customer_reviews_link' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
        ];
    }

}
