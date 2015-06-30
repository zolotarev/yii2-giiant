<?php
namespace zolotarev\giiant\crud\providers;

use yii\db\ColumnSchema;

/**
 * Class OptsProvider
 * @package zolotarev\giiant\crud\providers
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */
class OptsProvider extends \zolotarev\giiant\base\Provider
{
	public function activeField($attribute)
	{
		$column = $this->generator->getColumnByAttribute($attribute);
		if (!$column) {
			return null;
		}

		$modelClass = $this->generator->modelClass;
		$func = 'opts' . str_replace("_", "", $attribute);
		$camel_func = 'opts' . str_replace(' ', '', ucwords(implode(' ', explode('_', $attribute))));

		if (method_exists($modelClass::className(), $func)) {
			$mode = isset($this->columnNames[$attribute]) ? $this->columnNames[$attribute] : null;
		} elseif (method_exists($modelClass::className(), $camel_func)) {
			$func = $camel_func;
			$mode = isset($this->columnNames[$attribute]) ? $this->columnNames[$attribute] : null;
		} else {
			return null;
		}

		switch ($mode) {
			case 'radio':
				return <<<EOS
                    \$form->field(\$model, '{$attribute}')->radioList(
                        {$modelClass}::{$func}()
                    );
EOS;
				break;

			case 'select2':
				return <<<EOS
                    \$form->field(\$model, '{$attribute}')->widget(\kartik\select2\Select2::classname(), [
                        'name' => 'class_name',
                        'model' => \$model,
                        'attribute' => '{$attribute}',
                        'data' => {$modelClass}::{$func}(),
                        'options' => [
                            'placeholder' => {$this->generator->generateString('Type to autocomplete')},
                            'multiple' => false,
                        ]
                    ]);
EOS;
				break;

			default:
				// Render a dropdown list if the model has a method optsColumn().
				return <<<EOS
                        \$form->field(\$model, '{$attribute}')->dropDownList(
                            {$modelClass}::{$func}()
                        );
EOS;

		}

		return null;

	}

	/**
	 * Formatter for detail view attributes, who have get[..]ValueLabel function
	 * @param $column ColumnSchema
	 * @return null|string
	 */
	public function attributeFormat($attribute)
	{
		$modelClass = $this->generator->modelClass;
		$camel_func = 'get' . str_replace(' ', '', ucwords(implode(' ', explode('_', $attribute)))) . 'ValueLabel';

		if (!method_exists($modelClass::className(), $camel_func)) {
			return null;
		}

		return <<<EOS
            [
                'attribute'=>'{$attribute}',
                'value'=>{$modelClass}::{$camel_func}(\$model->{$attribute}),
            ]
EOS;
	}

	/**
	 * Formatter for detail view attributes, who have get[..]ValueLabel function
	 *
	 * @param $column ColumnSchema
	 * @param $model ActiveRecord
	 *
	 * @return null|string
	 */
	public function columnFormat($attribute, $model)
	{
		$modelClass = $this->generator->modelClass;
		$camel_func = 'get' . str_replace(' ', '', ucwords(implode(' ', explode('_', $attribute)))) . 'ValueLabel';

		if (!method_exists($modelClass::className(), $camel_func)) {
			return null;
		}

		return <<<EOS
			[
                'attribute'=>'{$attribute}',
                'value' => function (\$model) {
                    return {$modelClass}::{$camel_func}(\$model->{$attribute});
                }    
            ]        
EOS;


	}

}
