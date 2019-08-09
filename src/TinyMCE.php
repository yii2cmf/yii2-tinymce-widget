<?php
namespace yii2cmf\tinymce;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

class TinyMCE extends InputWidget
{
    public $plugins = ['code', 'table', 'media', 'image'];

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
        $this->getView()->registerJs("
            tinymce.init({
            selector: '#$options_id',
            plugins: '$plugins',
            language: '$lang',
            language_url: '$bundle->baseUrl/langs/$lang.js'
        });
        ", View::POS_END);
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

    private function getPlugins()
    {
        $plugins = '';
        foreach ($this->plugins as $plugin) {
            $plugins .= ','.$plugin;
        }
        return $plugins;
    }
}