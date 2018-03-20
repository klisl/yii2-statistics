<?php

namespace Klisl\Statistics;

use yii\web\AssetBundle;

class StatAssetsBundle extends AssetBundle
{

    public $sourcePath = '@vendor/klisl/yii2-statistics/assets';

    public $css = [
        'css/style_ip.css',
        'css/jquery.toastmessage.css'
    ];
    public $js = [
        'js/api-ip.js',
        'js/jquery.toastmessage.js'
    ];

}