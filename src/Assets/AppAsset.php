<?php

declare(strict_types=1);

namespace yii\demo\basic\Assets;

use yii\bootstrap5\BootstrapAsset;
use yii\jquery\web\YiiAsset;
use yii\web\AssetBundle;
use yii\web\View;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
    public $js = [
        'js/color-mode.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
