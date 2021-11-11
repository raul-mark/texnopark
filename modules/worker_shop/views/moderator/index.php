<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Модераторы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?=$this->title;?></h1>

        <ol class="breadcrumb">
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/'])?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><?=$this->title;?></li>
        </ol>
    </section>
    <section class="content">
        <?php if (Yii::$app->session->hasFlash('moderator_removed')) {?>
            <div class="alert alert-success text-center"><?=Yii::$app->session->getFlash('moderator_removed');?></div>
        <?php }?>
        <div class="box">
            <div class="box-header">
                <div class="pull-right">
                <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/moderator/create'])?>" class="btn btn-primary"><i class="fa fa-plus"></i> Добавить модератора</a>
                </div>
            </div>
            <div class="box-body">
                <div class="grid-table" style="overflow-x: scroll;">
                    
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'summary' => "Страница {begin} - {end} из {totalCount} модераторов<br/><br/>",
                            'emptyText' => 'Модераторы не найдены',
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
                                    'label' => 'Фото',
                                    'format' => 'html',
                                    'value' => function($data) { return Html::img($data->getPhoto('50x50'), ['width'=>'50']); },
                                ],
                                [
                                    'attribute'=>'id',
                                    'label'=>'<i class="fa fa-sort"></i> ID',
                                    'encodeLabel' => false,
                                ],
                                [
                                    'attribute'=>'name',
                                    'label'=>'<i class="fa fa-sort"></i> Имя',
                                    'encodeLabel' => false,
                                    'value' => function ($model, $key, $index, $column) {
                                        return ($model->{$column->attribute}) ? $model->{$column->attribute} : 'Не указано';
                                    },
                                ],
                                [
                                    'attribute'=>'lastname',
                                    'label'=>'<i class="fa fa-sort"></i> Фамилия',
                                    'encodeLabel' => false,
                                    'value' => function ($model, $key, $index, $column) {
                                        return ($model->{$column->attribute}) ? $model->{$column->attribute} : 'Не указано';
                                    },
                                ],
                                [
                                    'attribute'=>'login',
                                    'label'=>'<i class="fa fa-sort"></i> Логин',
                                    'encodeLabel' => false,
                                    'value' => function ($model, $key, $index, $column) {
                                        return ($model->{$column->attribute}) ? $model->{$column->attribute} : 'Не указано';
                                    },
                                ],
                                [
                                    'attribute'=>'date',
                                    'label'=>'<i class="fa fa-sort"></i> Дата',
                                    'encodeLabel' => false,
                                    'value' => function ($model, $key, $index, $column) {
                                        return ($model->{$column->attribute}) ? $model->{$column->attribute} : 'Не указано';
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
                                                        <li><a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/moderator/view', 'id'=>$model->id]).'" class="dropdown-item">Посмотреть</a></li>
                                                        <li><a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/moderator/create', 'id'=>$model->id]).'" class="dropdown-item">Редактировать</a></li>
                                                        <li><a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/moderator/remove', 'id'=>$model->id]).'" class="dropdown-item remove-object">Удалить</a></li>
                                                    </ul>';
                                        }
                                    ],
                                ]
                            ],
                        ]); ?>
                    
                </div>
            </div>
        </div>
    </section>
</div>