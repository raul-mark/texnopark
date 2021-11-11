<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = 'Контроль';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?=$this->title;?></h1>
        
        <ol class="breadcrumb">
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker/'])?>"><i class="fa fa-dashboard"></i> Главная</a></li>
            <li class="active"><?=$this->title;?></li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info color-palette-box">
            <div class="box-header with-border">
                <!-- <div class="box-title pull-right" style="font-size: 14px">
                    <a href="<?=Yii::$app->urlManager->createUrl(['/worker/notice/waybill-create'])?>" class="btn btn-primary">
                        <i class="fa fa-pencil"></i>
                        Создать заявку
                    </a>
                </div> -->
                <div id="action-links" style="display:none">
                    <!-- <a href="javascript:;" class="btn btn-danger" data-value="remove"><i class="fa fa-trash"></i> Удалить</a> -->
                    <!-- <a href="javascript:;" class="btn btn-warning" data-value="disable"><i class="fa fa-lock"></i> Заблокировать</a>
                    <a href="javascript:;" class="btn btn-success" data-value="enable"><i class="fa fa-unlock"></i> Разблокировать</a> -->
                </div>
            </div>
            <div class="box-body" id="item-block" style="overflow-y: auto;">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => "Страница {begin} - {end} из {totalCount} заявок<br/><br/>",
                    'emptyText' => 'Заявок нет',
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
                            'attribute'=>'id',
                            'label'=>'<i class="fa fa-sort"></i> ID',
                            'encodeLabel' => false,
                            'contentOptions' => [
                                'style' => 'width:70px'
                            ],
                        ],
                        [
                            'attribute'=>'notice_number',
                            'label'=>'<i class="fa fa-sort"></i> Номер извещения',
                            'encodeLabel' => false,
                            'value' => function ($model, $key, $index, $column) {
                                return $model->noticeTruck->notice_number ? $model->noticeTruck->notice_number : '-';
                            },
                        ],
                        [
                            'attribute'=>'truck_number',
                            'label'=>'<i class="fa fa-sort"></i> Номер фуры',
                            'encodeLabel' => false,
                            'value' => function ($model, $key, $index, $column) {
                                return $model->noticeTruck->noticeWaybill ? $model->noticeTruck->noticeWaybill->truck_number : '-';
                            },
                        ],
                        [
                            'attribute'=>'truck_number_reg',
                            'label'=>'<i class="fa fa-sort"></i> Рег. номер фуры',
                            'encodeLabel' => false,
                            'value' => function ($model, $key, $index, $column) {
                                return $model->noticeTruck->noticeWaybill ? $model->noticeTruck->noticeWaybill->truck_number_reg : '-';
                            },
                        ],
                        [
                            'attribute'=>'invoice_number',
                            'label'=>'<i class="fa fa-sort"></i> Номер инвойса',
                            'encodeLabel' => false,
                            'value' => function ($model, $key, $index, $column) {
                                return $model->noticeTruck->noticeWaybill ? $model->noticeTruck->noticeWaybill->invoice_number : '-';
                            },
                        ],
                        [
                            'attribute'=>'provider_id',
                            'label'=>'<i class="fa fa-sort"></i> Поставщик',
                            'encodeLabel' => false,
                            'value' => function ($model, $key, $index, $column) {
                                return $model->noticeTruck->noticeWaybill->provider ? $model->noticeTruck->noticeWaybill->provider->name_ru : '-';
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
                                } else if ($model->status == 2) {
                                    return '<small class="label label-danger">NG</small>';
                                } else {
                                    return '<small class="label label-danger">Не обработан</small>';
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
                                                <li><a href="'.Yii::$app->urlManager->createUrl(['/worker/notice/control-view', 'id'=>$model->id]).'" class="dropdown-item">Посмотреть</a></li>
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