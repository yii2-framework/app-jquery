<?php

declare(strict_types=1);

namespace app\Assets;

use yii\bootstrap5\BootstrapAsset;
use yii\jquery\web\YiiAsset;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Registers application CSS and JS assets with Bootstrap 5 and jQuery dependencies.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $depends = [
        BootstrapAsset::class,
        YiiAsset::class,
    ];
    public $js = [
        'js/color-mode.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
