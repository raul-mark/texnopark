<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?=$this->title;?></h1>

        <ol class="breadcrumb">
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/'])?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><?=$this->title;?></li>
        </ol>
    </section>
    <section class="content">
        <?php if (Yii::$app->session->hasFlash('worker_removed')) {?>
            <div class="alert alert-success text-center"><?=Yii::$app->session->getFlash('worker_removed');?></div>
        <?php }?>
        <div class="box">
            <div class="box-header">
                <div class="pull-right">
                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/worker/create'])?>" class="btn btn-primary"><i class="fa fa-plus"></i> Добавить сотрудника</a>
                </div>
            </div>
            <div class="box-body">
                <div class="grid-table">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'summary' => "Страница {begin} - {end} из {totalCount} сотрудников<br/><br/>",
                        'emptyText' => 'Сотрудники не найдено',
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
                                'attribute'=>'type',
                                'label'=>'<i class="fa fa-sort"></i> Тип',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->type == 'waybill') {
                                        return '<span class="label label-success">Накладная</span>';
                                    }
                                    if ($model->type == 'truck') {
                                        return '<span class="label label-success">Форма приёма грузовика</span>';
                                    }
                                    if ($model->type == 'control') {
                                        return '<span class="label label-success">Форма входного контроля</span>';
                                    }
                                    if ($model->type == 'act') {
                                        return '<span class="label label-success">Акт приемки</span>';
                                    }
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
                                                    <li><a href="'.Yii::$app->urlManager->createUrl(['/admin/worker/view', 'id'=>$model->id]).'" class="dropdown-item">Посмотреть</a></li>
                                                    <li><a href="'.Yii::$app->urlManager->createUrl(['/admin/worker/create', 'id'=>$model->id]).'" class="dropdown-item">Редактировать</a></li>
                                                    <li><a href="'.Yii::$app->urlManager->createUrl(['/admin/worker/remove', 'id'=>$model->id]).'" class="dropdown-item remove-object">Удалить</a></li>
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