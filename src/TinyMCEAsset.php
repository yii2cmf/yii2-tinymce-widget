<?php
namespace yii2cmf\tinymce;

use yii\web\AssetBundle;

class TinyMCEAsset extends AssetBundle
{

    public $sourcePath = '@vendor/tinymce/tinymce';

    public $js = [
        'jquery.tinymce.min.js',
        'tinymce.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}