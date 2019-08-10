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
    public $plugins = ['code', 'table', 'media', 'image', 'paste', 'imagetools', 'link', 'powerpaste', 'advlist'];

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
        $menubar = $this->getBoolToStr($this->menubar);
        $toolbar = $this->getBoolToStr($this->toolbar);

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
              language_url: \"$bundle->baseUrl/langs/$lang.js\"
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
}