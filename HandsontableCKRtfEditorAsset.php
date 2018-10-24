<?php
/**
 * @package yii2-widget-handsontable
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\handsontable;


use simialbi\yii2\web\AssetBundle;

class HandsontableCKRtfEditorAsset extends AssetBundle
{
    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $js = [
        'js/CKEditor.min.js'
    ];

    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $depends = [
        'dosamigos\ckeditor\CKEditorAsset'
    ];
}