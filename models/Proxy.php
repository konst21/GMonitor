<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
namespace app\models;

use app\components\Debig;
use yii\base\Model;
use yii;

/**
 * Class Esproxy based on Elasticsearch, not used yii2-elasticsearch AR
 * @package app\models
 */
class Proxy extends Esbase
{
    protected $index = 'esproxies';
    protected $type = 'esproxy';

    public function __construct()
    {
        $this->fields_mapping =
        [
            'proxy' => [
                'type' => 'string',
                'index' => 'not_analyzed',
            ],
            'bad_stat' => [
                'type' => 'integer'
            ],
            'captcha_stat' => [
                'type' => 'integer'
            ],
            'success_stat' => [
                'type' => 'integer'
            ],
        ];
        parent::__construct();
    }

    public function proxy_list_by_file ()
    {
        return;
        $proxies = explode(PHP_EOL, file_get_contents(yii::getAlias('@app/models/_proxy.txt')));
        foreach ($proxies as $proxy) {
            $row = [
                'proxy' => trim($proxy),
                'bad_stat' => 0,
                'captcha_stat' => 0,
                'success_stat' => 0,
            ];
            $this->insert_row($row);
        }
    }


    public function proxy_list()
    {
        $raw = $this->select_global('proxy');
        return array_keys($raw);
    }

    /**
     * @return array
     */
    public function country_stats()
    {
        $raw = $this->select_global('proxy');
        $countries = [];
        foreach ($raw as $proxy) {
            if (!array_key_exists($proxy['country'], $countries)) {
                $countries[$proxy['country']] = 0;
            }
            $countries[$proxy['country']]++;
        }
        return $countries;
    }

    public function all_proxy_data()
    {
        $raw = $this->select_global('proxy');
        return $raw;
    }

    public function random_proxy()
    {
        $list = $this->proxy_list();
        $keys = array_keys($list);
        return $list[mt_rand(min($keys), max($keys))];
    }

    public function increase_stat($proxy, $field_stat)
    {
        $id = array_keys($this->select_by_field_value('proxy', $proxy))[0];
        $this->increase_field_counter_by_id($id, $field_stat);
    }

    public function decrease_stat($proxy, $field_stat)
    {
        $id = array_keys($this->select_by_field_value('proxy', $proxy))[0];
        $this->decrease_field_counter_by_id($id, $field_stat);
    }

}
