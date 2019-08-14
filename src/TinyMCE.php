<?php
namespace yii2cmf\tinymce;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
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

        $selector = ['selector' => "#$options_id"];

        if (!empty($this->clientOptions)) {

            if (empty($this->clientOptions['images_upload_handler']) && isset($this->clientOptions['images_upload_url'])) {
                $script = new JsExpression($this->getImagesUploadHandler());
                $this->clientOptions['images_upload_handler'] = $script;
            }

            if (!empty($this->clientOptions['language']) && empty($this->clientOptions['language_url'])) {
                $lang = $this->getLanguage();
                $this->clientOptions['language_url'] = "$bundle->baseUrl/langs/$lang.js";
            }

            $options = Json::encode($selector+$this->clientOptions);
        } else {
            $options = Json::encode($selector);
        }
        $options = Json::encode(array_merge($selector,$this->clientOptions));

        $js = "tinymce.init($options)";

        $this->getView()->registerJs($js,View::POS_READY);
    }

    private function getLanguage()
    {
        return strlen(Yii::$app->language) > 2 ? substr(Yii::$app->language,0, strpos(Yii::$app->language,'-')) : Yii::$app->language;
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
        $images_upload_url = $this->clientOptions['images_upload_url'];

        return "function(blobInfo, success, failure){
            var xhr, formData;
                                               
                xhr = new XMLHttpRequest();
                xhr.withCridentials = false;
                xhr.open('POST', '$images_upload_url');
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