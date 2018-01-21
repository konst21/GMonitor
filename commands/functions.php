<?php
/**
 * in this file write your func.
 * all Yii2 classes are available inside func.
 */


/**
 * @param GearmanJob $job
 * @return string
 */
function example (\GearmanJob $job)
{
    $gmonitor = new \app\components\GMonitor();//usage Yii2 class example
    sleep(2);
    return '';
}


