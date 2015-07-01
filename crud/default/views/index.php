<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var schmunk42\giiant\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
*/

    $this->title = '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-index">

    <?=
    "<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>
    echo $this->render('_search', ['model' =>$searchModel]);
    ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New') ?>, ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="pull-right">

            <?php
            $items = [];
            $model = new $generator->modelClass;
            ?>
            <?php foreach ($generator->getModelRelations($model) AS $relation): ?>
                <?php
                // relation dropdown links
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if ($generator->isPivotRelation($relation)) {
                    $iconType = 'random';
                }
                $controller = $generator->pathPrefix . Inflector::camel2id(
                        StringHelper::basename($relation->modelClass),
                        '-',
                        true
                    );
                $route = $generator->createRelationRoute($relation,'index');
                $label      = Inflector::titleize(StringHelper::basename($relation->modelClass), '-', true);
                $items[] = [
                    'label' => '<i class="glyphicon glyphicon-' . $iconType . '"> ' . $label . '</i>',
                    'url'   => [$route]
                ]
                ?>
            <?php endforeach; ?>

            <?= "<?= \n" ?>
            \yii\bootstrap\ButtonDropdown::widget(
                [
                    'id'       => 'giiant-relations',
                    'encodeLabel' => false,
                    'label'    => '<span class="glyphicon glyphicon-paperclip"></span>' . <?= Yii::t('backend', 'Relations'),
                    'dropdown' => [
                        'options'      => [
                            'class' => 'dropdown-menu-right'
                        ],
                        'encodeLabels' => false,
                        'items'        => <?= \yii\helpers\VarDumper::export($items) ?>
                    ],
                ]
            );
            <?= "?>" ?>
        </div>
    </div>

    <?php if ($generator->indexWidgetType === 'grid'): ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>
            </div>

            <div class="panel-body">

                <div class="table-responsive">
                <?= "<?= " ?>GridView::widget([
                'layout' => '{summary}{pager}{items}{pager}',
                'dataProvider' => $dataProvider,
                'pager'        => [
                    'class'          => yii\widgets\LinkPager::className(),
                    'firstPageLabel' => <?= $generator->generateString('First') ?>,
                    'lastPageLabel'  => <?= $generator->generateString('Last') ?>
                ],
                'filterModel' => $searchModel,
                'columns' => [

                <?php
                $actionButtonColumn = <<<PHP
        [
            'class' => '{$generator->actionButtonClass}',
            'urlCreator' => function(\$action, \$model, \$key, \$index) {
                // using the column name as key, not mapping to 'id' like the standard generator
                \$params = is_array(\$key) ? \$key : [\$model->primaryKey()[0] => (string) \$key];
                \$params[0] = \Yii::\$app->controller->id ? \Yii::\$app->controller->id . '/' . \$action : \$action;
                return Url::toRoute(\$params);
            },
            'contentOptions' => ['nowrap'=>'nowrap']
        ],
PHP;

                // action buttons first
                echo $actionButtonColumn;

                $count = 0;
                echo "\n"; // code-formatting

                foreach ($generator->getTableSchema()->columns as $column) {
                    $format = trim($generator->columnFormat($column,$model));
                    if ($format == false) continue;
                    if (++$count < $generator->gridMaxColumns) {
                        echo "\t\t\t{$format},\n";
                    } else {
                        echo "\t\t\t/*{$format}*/\n";
                    }
                }

                ?>
                ],
            ]); ?>
                </div>

            </div>

        </div>


    <?php else: ?>

        <?= "<?= " ?> ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>

    <?php endif; ?>

</div>
