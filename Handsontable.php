<?php
/**
 * @package yii2-widget-handsontable
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 1.0
 */

namespace simialbi\yii2\handsontable;

use simialbi\yii2\widgets\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\JsExpression;
use Yii;

/**
 * Handsontable grid widget
 *
 * Usage:
 *
 * ```php
 * echo Handsontable::widget([
 *      'clientOptions' => [
 *          'data' => [
 *              ['A1', 'B1', 'C1'],
 *              ['A2', 'B2', 'C2']
 *          ],
 *          'colHeaders' => true,
 *          'rowHeaders' => true
 *      ]
 * ]);
 * ```
 *
 * @author Simon Karlen <simi.albi@gmail.com>
 * @since 1.0
 */
class Handsontable extends Widget {
	/**
	 * @var boolean|array|null Defines if the right-click context menu should be enabled. Context menu allows to
	 * create new row or column at any place in the grid among other features.
	 */
	public $contextMenu;

	/**
	 * @var boolean|array If set to true, it enables a possibility to merge cells. If set to an array of objects, it merges
	 * the cells provided in the objects.
	 */
	public $mergeCells = false;

	/**
	 * @var boolean Show styling options in context menu like "title row" etc.
	 */
	public $styling = false;

	/**
	 * @var array Translations strings of predefined actions
	 */
	private $actionStrings = [
		'row_above'     => 'Insert row above',
		'row_below'     => 'Insert row below',
		'col_left'      => 'Insert column on the left',
		'col_right'     => 'Insert column on the right',
		'remove_row'    => 'Remove row',
		'remove_col'    => 'Remove column',
		'undo'          => 'Undo',
		'redo'          => 'Redo',
		'merge'         => 'Merge cells',
		'unmerge'       => 'Unmerge cells',
		'style'         => 'Style',
		'set_heading'   => 'Set heading',
		'unset_heading' => 'Unset heading'
	];

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		$this->registerTranslations();
		$hsep = 4;
		if (is_bool($this->contextMenu) && $this->contextMenu === true) {
			$this->clientOptions['contextMenu'] = [
				'items' => [
					'row_above'  => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['row_above'])
					],
					'row_below'  => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['row_below'])
					],
					'hsep1'      => '---------',
					'col_left'   => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['col_left'])
					],
					'col_right'  => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['col_right'])
					],
					'hsep2'      => '---------',
					'remove_row' => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['remove_row'])
					],
					'remove_col' => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['remove_col'])
					],
					'hsep3'      => '---------',
					'undo'       => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['undo'])
					],
					'redo'       => [
						'name' => Yii::t('simialbi/handsontable/widget', $this->actionStrings['redo'])
					]
				]
			];
		} elseif (is_array($this->contextMenu) && !ArrayHelper::isAssociative($this->contextMenu)) {
			$this->clientOptions['contextMenu'] = [
				'items' => []
			];
			$hsep                               = 1;
			foreach ($this->contextMenu as $action) {
				$string = ArrayHelper::getValue($this->actionStrings, $action);
				if (is_null($string)) {
					if (preg_match('#^[\-]+$#', $action)) {
						$this->clientOptions['contextMenu']['items']['hsep'.($hsep++)] = $action;
					} else {
						$this->clientOptions['contextMenu']['items'][$action] = [];
					}
					continue;
				}

				$this->clientOptions['contextMenu']['items'][$action] = [
					'name' => Yii::t('simialbi/handsontable/widget', $string)
				];
			}
		}

		if ($this->mergeCells !== false) {
			$this->clientOptions['mergeCells']                         = $this->mergeCells;
			$this->clientOptions['contextMenu']['items']['hsep'.$hsep] = '---------';
			$this->clientOptions['contextMenu']['items']['mergeCells'] = [
				'name'     => new JsExpression('function () {
					var hot = this,
						sel = hot.getSelected(),
						info = hot.mergeCells.mergedCellInfoCollection.getInfo(sel[0], sel[1]);
					
					if (info) {
						return \''.Yii::t('simialbi/handsontable/widget', $this->actionStrings['unmerge']).'\';
					} else {
						return \''.Yii::t('simialbi/handsontable/widget', $this->actionStrings['merge']).'\';
					}
				}'),
				'callback' => new JsExpression('function () {
					var hot = this;
					hot.mergeCells.mergeOrUnmergeSelection(hot.getSelectedRange());
					hot.render();
					Handsontable.hooks.run(hot, \'afterChange\', null, \'edit\');
				}')
			];
		}
		if ($this->styling) {
			$this->clientOptions['renderer']                             = 'styleRenderer';
			$this->clientOptions['contextMenu']['items']['hsep'.$hsep++] = '---------';
			$this->clientOptions['contextMenu']['items']['style']        = [
				'name'    => Yii::t('simialbi/handsontable/widget', $this->actionStrings['style']),
				'submenu' => [
					'callback' => new JsExpression('function (key, options) {}'),
					'items'    => [
						[
							'name'     => new JsExpression('function () {
								var hot = this,
									sel = hot.getSelected(),
									meta = hot.getCellMeta(sel[0], sel[1]);
								
								if (!meta.style || !meta.style.heading) {
									return \''.Yii::t('simialbi/handsontable/widget', $this->actionStrings['set_heading']).'\';
								}
								
								return \''.Yii::t('simialbi/handsontable/widget', $this->actionStrings['unset_heading']).'\';
							}'),
							'callback' => new JsExpression('function (key, options) {
								var hot = this,
									sel = hot.getSelected(),
									meta = hot.getCellMeta(sel[0], sel[1]),
									heading = true;
									
								if (!meta.style) {
								} else {
									heading = !meta.style.heading;
								}
								
								for (var i = sel[0]; i <= sel[2]; i++) {
									for (var k = sel[1]; k <= sel[3]; k++) {
										meta = hot.getCellMeta(sel[0], sel[1]);
										if (!meta.style) {
											meta.style = {heading: heading};
										} else {
											meta.style.heading = heading;
										}
										hot.setCellMeta(i, k, \'style\', meta.style);
									}
								}
								
								hot.render();
								
								return true;
							}'),
							'key'      => 'style:heading'
						]
					]
				]
			];
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run() {
		$html = Html::tag('div', '', $this->options);

		$this->registerPlugin();

		return $html;
	}

	/**
	 * @inheritdoc
	 */
	protected function registerPlugin($pluginName = 'Handsontable') {
		$id      = $this->options['id'];
		$jsId    = Inflector::slug($id, '_', true);
		$view    = $this->view;
		$options = Json::encode($this->clientOptions);

		HandsontableAsset::register($view);

		$view->registerJs("$pluginName.renderers.registerRenderer('styleRenderer', function (hot, td, row, col, prop, value, cellProperties) {
			td.style = {};
			
			$pluginName.renderers.TextRenderer.apply(this, arguments);
			
			var meta = hot.getCellMeta(row, col);
			if (!meta.style) {
				return td;
			}
			
			if (meta.style.heading) {
				td.style.fontWeight = 'bold';
			}
			
			return td;
		});");
		$view->registerJs("\nvar hot$jsId = new $pluginName(jQuery('#$id').get(0), $options)");
		$this->registerClientEvents();
		$view->registerJs("\nhot$jsId.runHooks('afterInit');");
	}

	/**
	 * @inheritdoc
	 */
	protected function registerClientEvents() {
		if (!empty($this->clientEvents)) {
			$id   = $this->options['id'];
			$jsId = Inflector::slug($id, '_', true);
			$js   = [];
			foreach ($this->clientEvents as $event => $handler) {
				$js[] = "Handsontable.hooks.add('$event', $handler, hot$jsId);";
			}
			$this->view->registerJs(implode("\n", $js));
		}
	}
}