<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

use xj\qrcode\QRcode;
use xj\qrcode\widgets\Text;

use app\widgets\admin_language_tab\AdminLanguageTab;
use app\widgets\admin_shipment_menu\AdminShipmentMenu;

$this->title = 'Отгрузка';
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
        <?php if (Yii::$app->session->hasFlash('product_saved')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('product_saved');?>
            </div>
        <?php }?>

        <?php if ($model) {?>
            <div class="box box-info color-palette-box">
                <div class="box-header">
                    <?php if ($model->status == 0) {?>
                        <div class="pull-right">
                            <a href="javascript:;" class="btn btn-success" id="shipment-accept"><i class="fa fa-check"></i> Принять</a>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/shipment/confirmation', 'id'=>$model->id, 'type'=>'2']);?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i> Отклонить</a>
                        </div>
                    <?php }?>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <td>ID:</td>
                            <td><?=$model->id ? $model->id : '-';?></td>
                        </tr>
                        <tr>
                            <td>Ф.И.О:</td>
                            <td><?=$model->fio ? $model->fio : '-';?></td>
                        </tr>
                        <tr>
                            <td>Статус:</td>
                            <td>
                                <?php
                                    if ($model->status == 1) {
                                        echo '<small class="label label-success">Активный</small>';
                                    } else {
                                        echo '<small class="label label-warning">В ожидании</small>';
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата отгрузки:</td>
                            <td><?=$model->date_shipment ? $model->date_shipment : '-';?></td>
                        </tr>
                        <tr>
                            <td>Примечание:</td>
                            <td><?=$model->comment ? $model->comment : '-';?></td>
                        </tr>
                    </table>
                </div>
            </div>
        
            <div class="box box-info color-palette-box" id="products">
                <div class="box-header">
                    Товары
                </div>
                <div class="box-body" style="overflow-x: scroll;">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'summary' => "Страница {begin} - {end} из {totalCount} данных<br/><br/>",
                        'emptyText' => 'Данных нет',
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
                                'label'=>'<i class="fa fa-sort"></i> Товар',
                                'encodeLabel' => false,
                                'format' => 'html',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker/product/view', 'id'=>$model->product->id]).'">'.$model->product->name_ru.'</a>' : '-';
                                },
                            ],
                            [
                                'attribute'=>'amount',
                                'label'=>'<i class="fa fa-sort"></i> Кол-во',
                                'encodeLabel' => false,
                            ],
                            [
                                'attribute'=>'article',
                                'label'=>'<i class="fa fa-sort"></i> Артикул',
                                'encodeLabel' => false,
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
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
            <?php $form = ActiveForm::begin(); ?>
                <div id="product-form" style="display:none">
                    <?php if ($model->shipmentProducts) {?>
                        <?php foreach ($model->shipmentProducts as $k => $product) {?>
                            <div class="product-block">
                                <div class="box box-danger color-palette-box">
                                    <div class="box-body box-info color-palette-box">
                                        <table class="table table-striped">
                                            <tr>
                                                <td>ID</td>
                                                <td><?=$product->product_id;?></td>
                                            </tr>
                                            <tr>
                                                <td>Название</td>
                                                <td><?=$product->product->name_ru;?></td>
                                            </tr>
                                            <tr>
                                                <td>Артикул</td>
                                                <td><?=$product->product->article;?></td>
                                            </tr>
                                            <tr>
                                                <td>Кол-во</td>
                                                <td><?=$product->amount;?></td>
                                            </tr>
                                        </table>
                                        <hr/>
                                        <input type="hidden" name="ShopProduct[products][product][]" value="<?=$product->product_id?>"/>
                                        <input type="hidden" name="ShopProduct[products][amount][]" value="<?=$product->amount?>"/>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label">Номер стелажа</label>
                                                    <select name="ShopProduct[products][stack_id][]" class="form-control select-drop-stack" style="margin-top">
                                                        <option value="">Выбрать стелаж</option>
                                                        <?php foreach ($stacks as $stack) {?>
                                                            <option value="<?=$stack->id;?>"><?=$stack->stack_number;?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="control-label">Номер ячейки</label>
                                                    <select name="ShopProduct[products][shelf_id][]" class="form-control" style="margin-top">
                                                        <option value="">Выбрать ячейку</option>
                                                        <?php foreach ($shelvings as $shelf) {?>
                                                            <option value="<?=$shelf->id;?>"><?=$shelf->shelf_number;?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    <?php }?>
                    <?=Html::submitButton('<i class="fa fa-check"></i> Сохранить', ['class'=>'btn btn-primary']);?>
                </div>
            <?php ActiveForm::end();?>
        <?php } else {?>
            <div class="alert alert-warning text-center">Данных нет</div>
        <?php }?>
    </section>
</div>

<?php
$script = <<<JS
    $('#shipment-accept').on('click', function() {
        $('#product-form').toggle();
        $('#products').toggle();
    });

    $(document).on('change', '.select-drop-stack', function() {
        var id = $(this).val();
        var block = $(this).parent().parent().next().find('select');
        block.html('');

        if (id) {
            $.ajax({
                url: '/worker_shop/product/get-shelvings',
                type: 'post',
                data: {'id':id},
                success: function(data) {
                    if (data && data['data']) {
                        block.append('<option value="">Выбрать ячейку</option>');
                        for (var i in data['data']) {
                            block.append('<option value="'+data['data'][i]['id']+'">'+data['data'][i]['shelf_number']+'</option>');
                        }
                    }
                }
            });
        }

        return false;
    });
JS;

$this->registerJs($script);
?>