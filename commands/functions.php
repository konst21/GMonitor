<?php

/*function product2(\GearmanJob $job)
{
    $data = $job->workload();
    $asin = unserialize($data)['asin'];
    $parser = new \app\components\Parser();
    $product = new \app\components\Product($parser);
    $db = new \app\models\Product();
    //$proxy = new \app\models\Proxy();
    $cookies = $parser->web_page_get($product->root_url)['cookies'];
    echo $parser->proxy . "\n";
    //$proxy->increase_stat($parser->proxy, 'bad_stat');
    sleep(1);
    $url = $product->url_generate($asin);
    $html = $parser->web_page_get($url, $cookies)['content'];
    $product_fields = array_keys($product->fields());
    $out = [];
    $fail_counter = 0;
    $product->parser->xpath_create($html);
    foreach ($product_fields as $key) {
        @$content = $product->get_parsed_data($key, $html);
        $out[$key] = $content;
        if (empty($content) || !$content) $fail_counter++;
    }
    $out['asin'] = $asin;
    $out['time_stamp'] = time();
    $out['url'] = $url;
    $db->insert_row($out);
    if ($fail_counter < 5) {
        //$proxy->increase_stat($parser->proxy, 'success_stat');
        //$proxy->decrease_stat($parser->proxy, 'bad_stat');

    }
}*/

function product(\GearmanJob $job)
{
    $uniq_data = unserialize($job->workload());
    $parser = new \app\components\Parser();
    $parsed_object = new \app\components\Product($parser);
    $db = new \app\models\Product();
    work_parse($uniq_data, $parser, $parsed_object, $db);
}

function brand(\GearmanJob $job)
{
    $uniq_data = unserialize($job->workload());
    $parser = new \app\components\Parser();
    $parsed_object = new \app\components\Brand($parser);
    $db = new \app\models\Brand();
    work_parse($uniq_data, $parser, $parsed_object, $db);
}

function work_parse($uniq_data, app\components\Parser $parser, app\components\Parsed $parsed_object, app\models\Esbase $db)
{
    $proxy = new \app\models\Proxy();
    $cookies = $parser->web_page_get($parsed_object->root_url)['cookies'];
//    echo $parser->proxy . "\n";
    $proxy->increase_stat($parser->proxy, 'bad_stat');
    sleep(1);
    $url = $parsed_object->url_generate($uniq_data);
    $html = $parser->web_page_get($url, $cookies)['content'];
    $product_fields = array_keys($parsed_object->fields());
    $out = [];
    $fail_counter = 0;
    $parsed_object->parser->xpath_create($html);
    foreach ($product_fields as $key) {
        @$content = $parsed_object->get_parsed_data($key);
        $out[$key] = $content;
        if (empty($content) || !$content) $fail_counter++;
    }
    foreach ($uniq_data as $key => $value) {
        $out[$key] = $value;
    }
    $out['time_stamp'] = time();
    $out['url'] = $url;
    $db->insert_row($out);
    if ($fail_counter < 5) {
        $proxy->increase_stat($parser->proxy, 'success_stat');
    }
}

function asin_collect(\GearmanJob $job)
{
    $uniq_data = unserialize($job->workload());
    $parser = new \app\components\Parser();
    $asin = new \app\components\Asin($parser);

    $asin_db = new \app\models\Asin($uniq_data['merchant']);

    $proxy = new \app\models\Proxy();
    $cookies = $parser->web_page_get($asin->root_url)['cookies'];
    $proxy->increase_stat($parser->proxy, 'bad_stat');
    sleep(1);
    $url = $asin->url_generate($uniq_data);
    $html = $parser->web_page_get($url, $cookies)['content'];
    $parser->xpath_create($html);
    $block = $asin->parser->html('//*[@id="resultsCol"]');
    preg_match_all('/data-asin\=\"[^\"]{10,20}\"/', $block, $z);
    foreach ($z[0] as $asin) {
        $asin = str_replace(['data-asin="', '"'], '', $asin);
        //file_put_contents('/var/www/_dl/asins.txt', $asin . PHP_EOL, FILE_APPEND);
        if (!$asin_db->select_by_field_value('asin', $asin)) {
            $asin_db->insert_row([
                'asin' => $asin,
                'time_stamp' => time(),
            ]);
        }
    }

    if (count($z[0]) > 0) {
        $client = new \GearmanClient();
        $client->addServer('127.0.0.1');
        $uniq_data['page']++;
        $client->doBackground('asin_collect', serialize($uniq_data));
    }

}
