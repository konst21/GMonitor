<?php
namespace app\components;

use yii\base\Component;

use Yii;

class Debig extends Component {
    public function view ($var, $die = 0)
    {
        echo "<pre>" . print_r($var, 1) . "</pre>";
        if ($die) die();
    }
    public function dump ($var, $die = 0)
    {
        ob_start();
        var_dump($var);
        $result = ob_get_clean();
        echo "<pre>" . $result . "</pre>";
        if ($die) die();
    }
}