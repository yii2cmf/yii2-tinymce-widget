<?php
namespace yii2cmf\tinymce;

use yii\web\AssetBundle;

/**
 * Class TinyMCEWidgetAsset
 * @package yii2cmf\tinymce
 */
class TinyMCEWidgetAsset extends AssetBundle
{
    public $sourcePath = '@vendor/yii2cmf/yii2-tinymce-widget/src/assets/';

    public $depends = [
        '\yii2cmf\tinymce\TinyMCEAsset'
    ];

    public $publishOptions = [
        'forceCopy'=>true,
    ];
}