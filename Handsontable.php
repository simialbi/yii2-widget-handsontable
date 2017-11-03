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
	 * @var array Translations strings of predefined actions
	 */
	private $actionStrings = [
		'row_above'  => 'Insert row above',
		'row_below'  => 'Insert row below',
		'col_left'   => 'Insert column on the left',
		'col_right'  => 'Insert column on the right',
		'remove_row' => 'Remove row',
		'remove_col' => 'Remove column',
		'undo'       => 'Undo',
		'redo'       => 'Redo'
	];

	public function init() {
		parent::init();

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
			$this->clientOptions['contextMenu'] = [];
			$hsep = 1;
			foreach ($this->contextMenu as $action) {
				$string = ArrayHelper::getValue($this->actionStrings, $action);
				if (is_null($string)) {
					if (preg_match('#^[\-]+$#', $action)) {
						$this->clientOptions['contextMenu']['hsep'.($hsep++)] = $action;
					} else {
						$this->clientOptions['contextMenu'][$action] = [];
					}
					continue;
				}

				$this->clientOptions['contextMenu'][$action] = [
					'name' => Yii::t('simialbi/handsontable/widget', $string)
				];
			}
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

		$view->registerJs("var hot$jsId = new $pluginName(jQuery('#$id').get(0), $options)");
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