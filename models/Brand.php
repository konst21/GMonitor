<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
namespace app\models;

use app\components\Debig;
use yii;

class Brand extends Esbase
{
    public function __construct()
    {
        $this->index = 'brands';
        $this->type = 'brand';
        $this->fields_mapping = $this->fields_mapping();
        parent::__construct();
    }

    public function fields_mapping()
    {
        return [
            'seller' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'marketplaceID' =>
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
            'brand' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'review_positiv' =>
                [
                    'type' => 'float',
                ],
            'review_count' =>
                [
                    'type' => 'float',
                ],
            'info' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'business_name' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'business_type' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'trade_register_number' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'vat_number' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'representative_name' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'business_address' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
            'review_block' =>
                [
                    'type' => 'string',
                    'index' => 'not_analyzed',
                ],
        ];
    }
}