<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

$this->title = 'Акт приемки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?=$this->title;?></h1>
        
        <ol class="breadcrumb">
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/'])?>"><i class="fa fa-dashboard"></i> Главная</a></li>
            <li class="active"><?=$this->title;?></li>
        </ol>
    </section>
    <section class="content">
        <?php if (Yii::$app->session->hasFlash('act_saved')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('act_saved');?>
            </div>
        <?php }?>
        <?php if ($model) {?>
            <div class="box box-info color-palette-box">
                <div class="box-header">
                    <div class="pull-right">
                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/act-update', 'id'=>$model->id]);?>" class="btn btn-warning"><i class="fa fa-pencil"></i> Редактировать</a>
                    </div>
                    <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/waybill', 'id'=>$model->noticeControl->noticeTruck->noticeWaybill->id]);?>" class="btn btn-primary">Накладная</a>
                    <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/truck', 'id'=>$model->noticeControl->noticeTruck->id]);?>" class="btn btn-primary">Прием грузовика</a>
                    <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/control', 'id'=>$model->noticeControl->id]);?>" class="btn btn-primary">Контроль качества</a>
                    <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/act', 'id'=>$model->id]);?>" class="btn btn-success">Акт приемки</a>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <td>ID:</td>
                            <td><?=$model->id ? $model->id : '-';?></td>
                        </tr>
                        <tr>
                            <td>Номер извещения:</td>
                            <td><?=$model->noticeControl->noticeTruck->notice_number ? $model->noticeControl->noticeTruck->notice_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Дата накладной:</td>
                            <td><?=$model->noticeControl->noticeTruck->noticeWaybill->date_notice ? $model->noticeControl->noticeTruck->noticeWaybill->date_notice : '-';?></td>
                        </tr>
                        <tr>
                            <td>ID номер фуры:</td>
                            <td><?=$model->noticeControl->noticeTruck->noticeWaybill->truck_number ? $model->noticeControl->noticeTruck->noticeWaybill->truck_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Регистрационный номер фуры:</td>
                            <td><?=$model->noticeControl->noticeTruck->noticeWaybill->truck_number_reg ? $model->noticeControl->noticeTruck->noticeWaybill->truck_number_reg : '-';?></td>
                        </tr>
                        <tr>
                            <td>Номер инвойса:</td>
                            <td><?=$model->noticeControl->noticeTruck->noticeWaybill->invoice_number ? $model->noticeControl->noticeTruck->noticeWaybill->invoice_number : '-';?></td>
                        </tr>
                        <tr>
                            <td>Поставщик:</td>
                            <td><?=$model->noticeControl->noticeTruck->noticeWaybill->provider ? $model->noticeControl->noticeTruck->noticeWaybill->provider->name_ru : '-';?></td>
                        </tr>
                        <tr>
                            <td>Общее кол-во товара:</td>
                            <td><?=$model->noticeActProducts ? count($model->noticeActProducts) : '0';?></td>
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
            <?php if ($model->noticeControl->noticeTruck->noticeWaybill->description) {?>
                <div class="box box-info color-palette-box" style="margin-top:20px">
                    <div class="box-header">
                        Описание
                    </div>
                    <div class="box-body">
                        <?=$model->noticeControl->noticeTruck->noticeWaybill->description;?>
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
                                    return $model->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/admin/product/view', 'id'=>$model->product->id]).'">'.$model->product->name_ru.'</a>' : '-';
                                },
                            ],
                            [
                                'attribute'=>'product_id',
                                'label'=>'<i class="fa fa-sort"></i> Артикул',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/admin/product/view', 'id'=>$model->product->id]).'">'.$model->product->article.'</a>' : '-';
                                },
                            ],
                            [
                                'attribute'=>'unit_id',
                                'label'=>'<i class="fa fa-sort"></i> Ед. измерения',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->unit ? $model->unit->name_ru : '-';
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
                        ],
                    ]); ?>
                </div>
            </div>
        <?php } else {?>
            <div class="alert alert-warning text-center">Данных нет</div>
        <?php }?>
    </section>
</div>