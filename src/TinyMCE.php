<?php
namespace yii2cmf\tinymce;

use Yii;
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
    public $language;
    public $clientOptions;

    public $defaultPlugins = [
        'code', 'table', 'media', 'image', 'paste', 'imagetools', 'link', 'advlist'
    ];


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
        $plugins = $this->getPlugins();
        $external_pluging = $this->getExternalPlugins();
        $branding = $this->getBoolToString($this->getClientOption('branding', true));
        $menubar = $this->getBoolToString($this->getClientOption('menubar', true));
        $toolbar = $this->getBoolToString($this->getClientOption('toolbar', true));
        $height = $this->getClientOption('height', '400px');
        $width = $this->getClientOption('width', '100%');
        $file_picker_types = $this->getClientOption('file_picker_types', 'image');
        $automatic_uploads = $this->getBoolToString($this->getClientOption('automatic_uploads',false));
        $external_filemanager_path = $this->getExternalFilemanagerPath();

        $filemanager_title = $this->getClientOption('filemanager_title', 'File Manager');

        $image_upload_url = $this->getClientOption('image_upload_url','');
        $images_upload_handler = $this->getImagesUploadHandler();

        $this->getView()->registerJs("
            tinymce.init({
              selector: \"#$options_id\",
              plugins: '$plugins',
              height: \"$height\",
              width: \"$width\",
              branding: $branding,
              menubar: $menubar,
              toolbar: $toolbar,
              language: \"$lang\",
              language_url: \"$bundle->baseUrl/langs/$lang.js\",
              images_upload_url: '$image_upload_url',
              images_upload_handler: $images_upload_handler,
              automatic_uploads: $automatic_uploads,
              file_picker_types: '$file_picker_types' ,
              filemanager_title: '$filemanager_title',
              external_filemanager_path: '$external_filemanager_path',
              external_plugins: $external_pluging    
        });
        ", View::POS_READY);
    }

    private function getLanguage()
    {
        if ($this->language) {
            return $this->filterLanguage($this->language);
        }
        return $this->filterLanguage(\Yii::$app->language);
    }

    private function getPlugins()
    {
        if (isset($this->clientOptions['plugins'])) {
            return implode(',', $this->clientOptions['plugins']);
        }
        return implode(' ',$this->defaultPlugins);
    }

    private function getExternalPlugins()
    {
        if (isset($this->clientOptions['external_plugins'])) {
            return json_encode($this->clientOptions['external_plugins']);
        }
        return '';
    }

    private function getExternalFilemanagerPath()
    {
        if (isset($this->clientOptions['external_filemanager_path'])) {
            return $this->clientOptions['external_filemanager_path'];
        }
        return '';
    }

    private function getClientOption($option_name, $default)
    {
        if (isset($this->clientOptions[$option_name])) {
            return $this->clientOptions[$option_name];
        }
        return $default;
    }

    private function getBoolToString($val)
    {
        return $val ? 'true' : 'false';
    }

    private function filterLanguage($language)
    {
        if (strpos($language,'-')) {
            return substr($language,0, strpos($language,'-'));
        } elseif (strpos($language,'_')) {
            return substr($language,0, strpos($language,'_'));
        }
        else {
            return $language;
        }
    }

    private function getImagesUploadHandler()
    {
        $image_upload_url = $this->getClientOption('image_upload_url', '');
        return " function(blobInfo, success, failure){                
                var xhr, formData;
                                                
                xhr = new XMLHttpRequest();
                xhr.withCridentials = false;
                xhr.open('POST', '$image_upload_url');
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