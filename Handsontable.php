<?php
/**
 * @package yii2-widget-handsontable
 * @author Simon Karlen <simi.albi@gmail.com>
 * @version 1.0
 */

namespace simialbi\yii2\handsontable;

use simialbi\yii2\widgets\Widget;
use yii\bootstrap\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;

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