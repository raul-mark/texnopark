<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

$this->title = 'Контроль качества';
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
        <?php if (Yii::$app->session->hasFlash('control_notice_accepted')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('control_notice_accepted');?>
            </div>
        <?php }?>
        <?php if (Yii::$app->session->hasFlash('control_notice_product_accepted')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('control_notice_product_accepted');?>
            </div>
        <?php }?>
        <?php if (Yii::$app->session->hasFlash('control_notice_product_declined')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('control_notice_product_declined');?>
            </div>
        <?php }?>
        <?php if ($model) {?>
            <div class="box box-info color-palette-box">
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <td>ID:</td>
                            <td><?=$model->id ? $model->id : '-';?></td>
                        </tr>
                        <tr>
                            <td>Номер извещения:</td>
                            <td><?=$model->noticeTruck->notice_number ? $model->noticeTruck->notice_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Дата накладной:</td>
                            <td><?=$model->noticeTruck->noticeWaybill->date_notice ? $model->noticeTruck->noticeWaybill->date_notice : '-';?></td>
                        </tr>
                        <tr>
                            <td>ID номер фуры:</td>
                            <td><?=$model->noticeTruck->noticeWaybill->truck_number ? $model->noticeTruck->noticeWaybill->truck_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Регистрационный номер фуры:</td>
                            <td><?=$model->noticeTruck->noticeWaybill->truck_number_reg ? $model->noticeTruck->noticeWaybill->truck_number_reg : '-';?></td>
                        </tr>
                        <tr>
                            <td>Номер инвойса:</td>
                            <td><?=$model->noticeTruck->noticeWaybill->invoice_number ? $model->noticeTruck->noticeWaybill->invoice_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Поставщик:</td>
                            <td><?=$model->noticeTruck->noticeWaybill->provider ? $model->noticeTruck->noticeWaybill->provider->name_ru : '-';?></td>
                        </tr>
                        <tr>
                            <td>Статус:</td>
                            <td>
                                <?php
                                    if ($model->status == 1) {
                                        echo '<span class="label label-success">Активный</span>';
                                    } else {
                                        echo '<span class="label label-danger">Не обработан</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php if ($model->noticeTruck->noticeWaybill->description) {?>
                <div class="box box-info color-palette-box" style="margin-top:20px">
                    <div class="box-header">
                        Описание
                    </div>
                    <div class="box-body">
                        <?=$model->noticeTruck->noticeWaybill->description;?>
                    </div>
                </div>
            <?php }?>
            <div class="box box-info color-palette-box">
                <div class="box-header">
                    Продукция
                </div>
                <div class="box-body" style="overflow-x: scroll;">
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
                                'attribute'=>'id',
                                'label'=>'<i class="fa fa-sort"></i> ID',
                                'encodeLabel' => false,
                                'contentOptions' => [
                                    'style' => 'width:70px'
                                ],
                            ],
                            [
                                'attribute'=>'product_id',
                                'label'=>'<i class="fa fa-sort"></i> Название',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/product/view', 'id'=>$model->product->id]).'">'.$model->product->name_ru.'</a>' : '-';
                                },
                            ],
                            [
                                'attribute'=>'product_id',
                                'label'=>'<i class="fa fa-sort"></i> Артикул',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/product/view', 'id'=>$model->product->id]).'">'.$model->product->article.'</a>' : '-';
                                },
                            ],
                            [
                                'attribute'=>'amount',
                                'label'=>'<i class="fa fa-sort"></i>  Кол-во',
                                'encodeLabel' => false,
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->amount ? $model->amount : '0';
                                },
                            ],
                            [
                                'attribute'=>'status',
                                'label'=>'<i class="fa fa-sort"></i>  Статус',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    if ($model->status == 1) {
                                        return '<span class="label label-success">Подтвержден</span>';
                                    } else {
                                        return '<span class="label label-danger">Отклонен</span>';
                                    }
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return '<a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/notice/control-accept-product', 'id'=>$model->id]).'" class="btn btn-success"><i class="fa fa-check"></i></a></li>
                                                <a href="'.Yii::$app->urlManager->createUrl(['/worker_shop/notice/control-decline-product', 'id'=>$model->id]).'" class="btn btn-danger"><i class="fa fa-remove"></i></a></li>';
                                    }
                                ],
                            ]
                        ],
                    ]); ?>
                </div>
                <?php if ($model->status == 0) {?>
                    <div class="box-footer">
                        <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/notice/control-accept', 'id'=>$model->id]);?>" class="btn btn-success">Подтвердить</a>
                    </div>
                <?php }?>
            </div>
        <?php } else {?>
            <div class="alert alert-warning text-center">Данных нет</div>
        <?php }?>
    </section>
</div>