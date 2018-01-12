<?php

/* @var $this yii\web\View */


$this->title = 'Gearman Monitor';
$this->registerJsFile(
    '@web/js/underscore.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        '@web/js/gmonitor.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerCssFile(
        '@web/css/gmonitor.css',
        []);
$this->registerCssFile(
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css',
        []);
$this->registerCssFile(
        '@web/css/font-awesome/css/font-awesome.css',
        []);
$this->registerLinkTag([
        'rel' => 'shortcut icon',
        'type' => 'image/x-icon',
        'href' => '/img/favicon.ico']);
?>
<?=$js_templates?>
<div>


    <div style="clear: both"></div>
    <div style="float: left">
        <table class="table table-bordered" style="width: auto;">
            <thead>
            <tr>
                <th style="width: 100px;">Count</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input class="form-control" id="worker_start_count" value="1"></td>
                <td><button class="btn btn-info"  id="worker_add">Add Workers</button></td>
            </tr>
            <tr>
                <td><input class="form-control" id="worker_set_count_val" value="1"></td>
                <td><button class="btn btn-success" id="worker_set_count">Set Workers Count</button></td>
            </tr>
            <tr>
                <td></td>
                <td><button class="btn btn-danger" id="worker_stop">Stop Workers</button></td>
            </tr>
            <tr>
                <td></td>
                <td><button class="btn btn-danger" id="total_reset">Total Reset</button></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="float: left; margin-left: 20px;">
        <div  id="functions_table_wrap"></div>
    </div>
    <div style="clear: both"></div>


</div>

