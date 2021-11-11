<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = 'Отдел ГП';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?=$this->title;?></h1>
        
        <ol class="breadcrumb">
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/'])?>"><i class="fa fa-dashboard"></i> Главная</a></li>
            <li class="active"><?=$this->title;?></li>
        </ol>
    </section>
    <section class="content">
        <?php if (Yii::$app->session->hasFlash('product_removed')) {?>
            <div class="callout callout-success text-center">
                <?=Yii::$app->session->getFlash('product_removed');?>
            </div>
        <?php }?>
        <div class="box box-info color-palette-box">
            
            <div class="box-body" id="item-block" style="overflow-x: scroll;">
                
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'summary' => "Страница {begin} - {end} из {totalCount} товаров<br/><br/>",
                        'emptyText' => 'Товаров нет',
                        'pager' => [
                            'options'=>['class'=>'pagination'],
                            'pageCssClass' => 'page-item',
                            'prevPageLabel' => 'Назад',
                            'nextPageLabel' => 'Вперед',
                            'maxButtonCount'=>10,
                            'linkOptions' => [
                                'class' => 'page-link'
                            ]
                         ],
                        'tableOptions' => [
                            'class'=>'table table-striped'
                        ],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'class' => 'yii\grid\CheckboxColumn'
                            ],
                            [
                                'label' => 'Фото',
                                'format' => 'html',
                                'value' => function($data) { return Html::img($data->getPhoto('50x50'), ['width'=>'50']); },
                            ],
                            [
                                'attribute'=>'id',
                                'label'=>'<i class="fa fa-sort"></i> ID',
                                'encodeLabel' => false,
                                'contentOptions' => [
                                    'style' => 'width:70px'
                                ],
                            ],
                            [
                                'attribute'=>'name_ru',
                                'label'=>'<i class="fa fa-sort"></i> Название',
                                'encodeLabel' => false,
                            ],
                            [
                                'attribute'=>'stock_id',
                                'label'=>'<i class="fa fa-sort"></i> Ряд',
                                'encodeLabel' => false,
                                'contentOptions' => [
                                    'style' => 'width:100px'
                                ],
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->stock ? $model->stock->name_ru : '-';
                                },
                            ],
                            [
                                'attribute'=>'stack_id',
                                'label'=>'<i class="fa fa-sort"></i>  Этаж',
                                'encodeLabel' => false,
                                'contentOptions' => [
                                    'style' => 'width:70px'
                                ],
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->stack ? $model->stack->stack_number : '-';
                                },
                            ],
                            [
                                'attribute'=>'shelf_id',
                                'label'=>'<i class="fa fa-sort"></i>  Ячека',
                                'encodeLabel' => false,
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->stackShelf ? $model->stackShelf->shelf_number : '-';
                                },
                            ],
                            [
                                'attribute'=>'article',
                                'label'=>'<i class="fa fa-sort"></i> Артикул',
                                'encodeLabel' => false,
                            ],
                            [
                                'attribute'=>'manufacturer_id',
                                'label'=>'<i class="fa fa-sort"></i> Поизводитель',
                                'encodeLabel' => false,
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->manufacturer ? $model->manufacturer->name_ru : '-';
                                },
                            ],
                            [
                                'attribute'=>'status',
                                'label'=>'<i class="fa fa-sort"></i> Статус',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'contentOptions' => [
                                    'style' => 'width:100px'
                                ],
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->status == 1) {
                                        return '<small class="label label-success">Активный</small>';
                                    } else {
                                        return '<small class="label label-danger">Заблокирован</small>';
                                    }
                                },
                            ],
                            [
                                'attribute'=>'date',
                                'label'=>'<i class="fa fa-sort"></i> Дата',
                                'encodeLabel' => false,
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->date;
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                    <span class="fa fa-cog"></span>
                                                </button>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/product/view', 'id'=>$model->id]).'" class="dropdown-item">Посмотреть</a></li>
                                                </ul>';
                                    }
                                ],
                            ]
                        ],
                    ]); ?>
                
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="add-product">
    <div class="modal-dialog">
        <div class="modal-content" id="category-add-block">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="modal-body">
                    <?=$form->field($model, 'file')->fileInput()->label('Товары (.xls)');?>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</a>
                    <?=Html::submitButton('<i class="fa fa-check-square-o"></i> Сохранить', ['class'=>'btn btn-primary']);?>
                </div>
            <?php ActiveForm::end();?>
        </div>
    </div>
</div>