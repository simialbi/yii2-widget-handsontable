<?php
/**
 * @package yii2-widget-handsontable
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 1.0
 */

namespace simialbi\yii2\handsontable;

use simialbi\yii2\web\AssetBundle;

/**
 * Class HandsontableAsset
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 1.0
 */
class HandsontableAsset extends AssetBundle
{
    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     */
    public $sourcePath = '@bower/handsontable/dist';

    /**
     * @var array list of CSS files that this bundle contains
     */
    public $css = [
        'pikaday/pikaday.css',
        'handsontable.min.css'
    ];

    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $js = [
        'numbro/languages.min.js',
        'numbro/numbro.js',
        'pikaday/pikaday.js',
        'handsontable.min.js'
    ];

    /**
     * @var array list of JavaScript files that this bundle contains.
     */
    public $depends = [
        'simialbi\yii2\web\MomentAsset'
    ];

    /**
     * @var array the options to be passed to [[AssetManager::publish()]] when the asset bundle
     * is being published.
     */
    public $publishOptions = [
        'only' => [
            'numbro/languages.min.js',
            'numbro/numbro.js',
            'pikaday/*',
            'handsontable.min.css',
            'handsontable.min.js'
        ]
    ];
}