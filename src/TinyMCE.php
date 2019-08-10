<?php
namespace yii2cmf\tinymce;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class TinyMCE
 * @package yii2cmf\tinymce
 */
class TinyMCE extends InputWidget
{
    public $width = '100%';
    public $height = '400px';
    public $branding = false;
    public $menubar = true;
    public $toolbar = true;
    public $image_upload_url;
    public $plugins = ['code', 'table', 'media', 'image', 'paste', 'imagetools', 'link', 'advlist'];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $this->registerPlugin();
    }

    private function registerPlugin()
    {
        $bundle = TinyMCEWidgetAsset::register($this->getView());

        $options_id = $this->options['id'];
        $lang = $this->getLanguage();
        $plugins = $this->getPluginsString();
        $branding = $this->getBoolToStr($this->branding);
        $menubar = is_bool($this->menubar) ? $this->getBoolToStr($this->menubar) : $this->menubar;
        $toolbar = is_bool($this->toolbar) ? $this->getBoolToStr($this->toolbar) : $this->toolbar;

        $image_upload_url = $this->image_upload_url;
        $images_upload_handler = $this->getImagesUploadHandler();

        $this->getView()->registerJs("
            tinymce.init({
              selector: \"#$options_id\",
              plugins: \"$plugins\",
              height: \"$this->height\",
              width: \"$this->width\",
              branding: $branding,
              menubar: $menubar,
              toolbar: $toolbar,
              language: \"$lang\",
              language_url: \"$bundle->baseUrl/langs/$lang.js\",
              images_upload_url: '$image_upload_url',
              images_upload_handler: $images_upload_handler,
              automatic_uploads: true,
              file_picker_types: 'image',
        });
        ", View::POS_READY);
    }

    private function getLanguage()
    {
        if (strpos(\Yii::$app->language,'-')) {
            return substr(\Yii::$app->language,0, strpos(\Yii::$app->language,'-'));
        } elseif (strpos(\Yii::$app->language,'_')) {
            return substr(\Yii::$app->language,0, strpos(\Yii::$app->language,'_'));
        }
        else {
            return \Yii::$app->language;
        }
    }

    private function getPluginsString()
    {
        return implode(' ', $this->plugins);
    }

    private function getBoolToStr($bool)
    {
        return boolval($bool) ? 'true' : 'false';
    }

    private function getImagesUploadHandler()
    {
        return " function(blobInfo, success, failure){                
                var xhr, formData;
                                                
                xhr = new XMLHttpRequest();
                xhr.withCridentials = false;
                xhr.open('POST', '$this->image_upload_url');
                xhr.onload = function () {
                    var json;
                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    
                    json = JSON.parse(xhr.responseText);
                    
                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                
                formData = new FormData();
                formData.append('_csrf', yii.getCsrfToken());
                formData.append('editor_file', blobInfo.blob(), blobInfo.filename());
                                
                xhr.send(formData);
            }";
    }
}