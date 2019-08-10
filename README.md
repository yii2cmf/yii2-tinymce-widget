TinyMCE WYSIWYG widget for Yii2
===============================
TinyMCE widget for Yii2.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2cmf/yii2-tinymce-widget "*"
```

or add

```
"yii2cmf/yii2-tinymce-widget": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= $form->field($model, 'post_content')->widget(\yii2cmf\tinymce\TinyMCE::class) ?>
```
With config
```php
<?= $form->field($model, 'post_content')->widget(\yii2cmf\tinymce\TinyMCE::class, ['height' => '400px', 'width' => '100%', 'plugins' => ['code', 'table', 'media', 'image']]) ?>
```
