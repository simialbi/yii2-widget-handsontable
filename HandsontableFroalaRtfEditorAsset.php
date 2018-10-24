<?php
/**
 * @package yii2-widget-handsontable
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\handsontable;


use simialbi\yii2\web\AssetBundle;

class HandsontableFroalaRtfEditorAsset extends AssetBundle
{
    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $js = [
        'js/FroalaEditor.min.js'
    ];

    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $depends = [
        'froala\froalaeditor\FroalaEditorAsset'
    ];
}