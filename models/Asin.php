<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
namespace app\models;

use app\components\Debig;
use yii;

class Asin extends Esbase
{
    public function __construct($merchant)
    {
        $this->index = 'asins';
        $this->type = $merchant;
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
            'time_stamp' =>
                [
                    'type' => 'integer',
                ],

        ];
    }

    public function all_asins()
    {
        return $this->select_global('asin');
    }

}
