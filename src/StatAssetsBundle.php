<?php

namespace Klisl\Statistics;

use yii\web\AssetBundle;

/**
 * Ресурсы расширения
 * @package Klisl\Statistics
 */
class StatAssetsBundle extends AssetBundle
{
    /** @var string  */
    public $sourcePath = '@vendor/klisl/yii2-statistics/assets';

    /** @var array  */
    public $css = [
        'css/style_ip.css',
        'css/jquery.toastmessage.css'
    ];
    public $js = [
        'js/api-ip.js',
        'js/jquery.toastmessage.js'
    ];

}