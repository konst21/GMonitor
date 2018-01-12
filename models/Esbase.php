<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/

namespace app\models;

use app\components\Debig;
use yii;

class Esbase
{
    /**
     * Elasticsearch php client
     * @var
     */
    protected $client;

    /**
     * Elasticsearch index
     * analog "database name" in SQL
     * @var string
     */
    protected $index;

    /**
     * analog "table name" in SQL
     * Elasticsearch type
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $fields_mapping = [];

    public $parh_to_elasticsearch = "/home/konst20/es3/bin/elasticsearch";

    /**
     * run Elasticsearch in
     * @var string
     */
    public $elastic_start_command = 'exec /home/konst20/es3/bin/elasticsearch &> /dev/null &';

    public function start_elasticsearch()
    {
        $command = "exec {$this->parh_to_elasticsearch} &> /dev/null &";
        shell_exec($command);
    }

    public function get_elasticsearch_process_id()
    {
        $command = "ps ax | grep Elasticsearch";
        exec($command, $output);
        $id = count($output)>2?explode(' ', trim($output[0]))[0]:false;
        return $id;
    }

    /**
     * Esbase constructor.
     */
    public function __construct()
    {
        $this->client = \Elasticsearch\ClientBuilder::create()->build();
    }

    public function check_db ()
    {
        try {
            $mapping = $this->client->indices()->getMapping(['index' => $this->index]);
        }
        catch (\Exception $e) {
            //@todo logging exception $e->getMessage()
            return 'fatal';
        }
        if (isset($mapping[$this->index]['mappings'][$this->type]['properties']) &&
                    count($mapping[$this->index]['mappings'][$this->type]['properties']) > 0) {
            return 'ok';
        }
        return 'no_mapping';
    }

    /**
     * create /index/type and mapping needed fields
     * @param $fields_mapping mixed array/bool
     * @return array|bool
     */
    public function create_db_mapping_index()
    {
/*
            $fileds_mapping_example = [
            'proxy' => [//field name
                'type' => 'string',
                'index' => 'not_analyzed',//this field save as whole string and not search by Lexic analyzer.
            ],
            'stat' => [//field name
                'type' => 'integer'
            ],
        ];
*/
        $params = [
            'index' => $this->index,
            'body' => [
                'mappings' => [
                    $this->type => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => $this->fields_mapping,
                    ],
                ],
            ],
        ];
        try {
            return $this->client->indices()->create($params);
        }
        catch (\Exception $e) {
            //@todo loggin exception message
            return false;
        }

    }

    /**
     *
     * @param $row array [field_name=>value,field_name=>value,field_name=>value,...]
     */
    public function insert_row($row)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => $row,
        ];

        $this->client->index($params);
    }

    /**
     * @param $conditions array format: example see below. $conditions = [] gives all rows in /index/type
     * @param array $sort format: [field_name => <asc OR desc>, field_name => <asc OR desc>,...]
     * @param array $limit format: [9] - limit 9 records from 0, or [10, 20] - records from 10 to 20, as in SQL
     * @return array|bool
     */
    public function select_global($id_key = 'id', $conditions = [], $sort = [], $limit = [])
    {
        /*
        $conditions_exapmle =
            [
                [
                    'match' => ['proxy' => '188.68.3.145:8085'],//strong condition
                ],
                [
                    'match' => ['stat' => 0],//strong condition
                ],
                [
                    'range' => [ //condition by range
                        'stat' => [
                            'gte' => -1,
                            'lte' => 1,
                        ],
                    ],
                ],
            ];
        */

        switch (count($limit)) {
            case 0:
                $from = 0;
                $to = 9999;
                break;
            case 1:
                $from = 0;
                $to = $limit[0];
                break;
            case 2:
                $from = $limit[0];
                $to = $limit[1];
                break;
            default:
                $from = 0;
                $to = 9999;
        }

        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'from' => $from,
            'size' => $to,
            'body' => [
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $conditions,
                    ],
                ],
            ],
        ];

        try {
            $raw = $this->client->search($params);
        }
        catch (\Exception $e) {
            //@todo logging exception $e->getMesssage()
            return false;
        }
        if ($raw['hits']['total'] == 0) return false; //yii::$app->debig->dump($raw, 1);
        $out = [];
        foreach ($raw['hits']['hits'] as $item) {
            $subout = [];
            $subout['id'] = $item['_id'];
            foreach ($item['_source'] as $field => $value) {
                $subout[$field] = $value;
            }
            $out[$subout[$id_key]] = $subout;
        }

        return $out;
    }

    /**
     * @param $field
     * @param $value
     * @return array|bool return array of rows, format
     * [id => [field=>value,field=>value],id => [field=>value,field=>value], ...], or false overwise
     */
    public function select_by_field_value($field, $value, $id_key = 'id')
    {
        $conditions = [
            [
                'match' => [$field => $value],
            ],
        ];

        return $this->select_global($id_key, $conditions);
    }

    /**
     * return row
     * @param $id
     * @return array|bool format ['id'=>$id, field=>value, field=>value]
     */
    public function select_by_id($id)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
        ];
        try {
            $data = $this->client->get($params);
        }
        catch (\Exception $e) {
            return false;
        }
        $out = [];
        $out['id'] = $id;
        foreach ($data['_source'] as $field => $value) {
            $out[$field] = $value;
        }
        return $out;
    }

    /**
     * @param $id
     */
    public function delete_by_id ($id)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
        ];
        try {
            $this->client->delete($params);
        }
        catch (\Exception $e) {
            return; //$id is not exists
        }
    }

    /**
     * @return bool
     */
    public function delete_db ()
    {
        $params = [
            'index' => $this->index,
        ];
        try {
            $response = $this->client->indices()->delete($params);
            return $response['acknowledged'];
        }
        catch (\Exception $e) {
            //@todo exception
            return false;
        }
    }

    /**
     * Remove all records
     */
    public function clear_db ()
    {
        $this->delete_db();
        $this->create_db_mapping_index();
    }

    /**
     * update field value OR add new field with value
     * @param $id
     * @param $field
     * @param $value
     */
    public function update_by_id($id, $field, $value)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'doc' => [
                    $field => $value
                ]
            ]
        ];

        $this->client->update($params);
    }

    public function increase_field_counter_by_id($id, $field, $number = 1)
    {
        $counter_string = "ctx._source.$field";
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'script' => "$counter_string += $number",
/*                'params' => [
                    'count' => $number
                ]*/
            ]
        ];

        $this->client->update($params);
    }

    public function decrease_field_counter_by_id($id, $field, $number = 1)
    {
        $counter_string = "ctx._source.$field";
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'script' => "$counter_string -= $number",
/*                'params' => [
                    'count' => $number
                ]*/
            ]
        ];

        $this->client->update($params);
    }

/*    protected function fields_mapping( \app\components\Parsed $parsed ){
        $parsed_fields = array_keys($parsed->fields(''));
    }*/

}
