<?php

namespace Klisl\Statistics;

use yii\web\AssetBundle;

class StatAssetsBundle extends AssetBundle
{

    public $sourcePath = '@vendor/klisl/yii2-statistics/public';

    // Путь в файловой системе до директории ресурсов
//    public $basePath = '@vendor/klisl/yii2-statistics/public';

    // Путь из web до директории ресурсов
//    public $baseUrl = '/public';

    // путь к JS файлам относительно basePath
//    public $js = [
//        'js/some-script.js'
//    ];

    // путь к CSS файлам относительно basePath
    public $css = [
        'css/style_ip.css'
    ];
}