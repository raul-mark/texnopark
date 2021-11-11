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
            <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker/'])?>"><i class="fa fa-dashboard"></i> Главная</a></li>
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
                                    } else if ($model->status == 2) {
                                        echo '<span class="label label-danger">NG</span>';
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
            <input type="hidden" id="control-status" value="<?=$model->status;?>"/>
            <?php if ($products) {?>
                <h3>Продукты</h3>
                <?php if ($model->status != 0) {?>
                    <?php foreach ($products as $product) {?>
                        <div class="box box-warning color-palette-box" style="margin-top:20px">
                            <div class="box-header">
                                Продукт #<?=$product->id;?>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td>ID</td>
                                        <td><?=$product->id;?></td>
                                    </tr>
                                    <tr>
                                        <td>Навзание</td>
                                        <td><?=$product->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker/product/view', 'id'=>$product->product->id]).'">'.$product->product->name_ru.'</a>' : '-';?></td>
                                    </tr>
                                    <tr>
                                        <td>Артикул</td>
                                        <td><?=$product->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker/product/view', 'id'=>$product->product->id]).'">'.$product->product->article.'</a>' : '-';?></td>
                                    </tr>
                                    <tr>
                                        <td>Кол-во</td>
                                        <td><?=$product->amount ? $product->amount : '0';?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        Процент продукта для проверки: <strong><?=$product->percentage ? $product->percentage.'%' : '<span style="color:#cc0000">Не указано</span>';?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        Кол-во проверяемого продукта: <strong><?=$product->amount_product ? $product->amount_product : '<span style="color:#cc0000">Не указано</span>';?></strong>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-sm-6">
                                        Процент дефектной продукции: <strong><?=$product->percentage_defect ? $product->percentage_defect.'%' : '<span style="color:#cc0000">Не указано</span>';?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        Кол-во дефектной продукции: <strong><?=$product->amount_defect ? $product->amount_defect : '<span style="color:#cc0000">Не указано</span>';?></strong>
                                    </div>
                                </div>
                                <br/>
                                Причина: <?=$product->reason ? $product->reason : '<strong style="color:#cc0000">Не указано</strong>';?>
                            </div>
                        </div>
                    <?php }?>
                    <?php if ($model->status == 2) {?>
                        <button id="product-edit" class="btn btn-primary"><i class="fa fa-pencil"></i> Редактировать</button>
                    <?php }?>
                <?php }?>
                <div id="product-form" style="display:<?=($model->status != 0) ? 'none' : 'block';?>">
                    <?php $form = ActiveForm::begin(['options'=>['onkeydown'=>"return event.key != 'Enter'"]]); ?>
                        <?php foreach ($products as $product) {?>
                            <div class="box box-warning color-palette-box" style="margin-top:20px">
                                <div class="box-header">
                                    Продукт #<?=$product->id;?>
                                </div>
                                <div class="box-body">
                                    <table class="table">
                                        <tr>
                                            <td>ID</td>
                                            <td><?=$product->id;?></td>
                                        </tr>
                                        <tr>
                                            <td>Навзание</td>
                                            <td><?=$product->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker/product/view', 'id'=>$product->product->id]).'">'.$product->product->name_ru.'</a>' : '-';?></td>
                                        </tr>
                                        <tr>
                                            <td>Артикул</td>
                                            <td><?=$product->product ? '<a href="'.Yii::$app->urlManager->createUrl(['/worker/product/view', 'id'=>$product->product->id]).'">'.$product->product->article.'</a>' : '-';?></td>
                                        </tr>
                                        <tr>
                                            <td>Кол-во</td>
                                            <td><?=$product->amount ? $product->amount : '0';?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="box-footer">
                                    <input type="hidden" class="product-amount-<?=$product->id;?>" value="<?=$product->amount;?>"/>
                                    <input type="hidden" class="product-percentage-<?=$product->id;?>" value="<?=$product->percentage;?>"/>
                                    <input type="hidden" class="product-amount-pr-<?=$product->id;?>" value="<?=$product->amount_product;?>"/>
                                    <input type="hidden" class="product-percentage-defect-<?=$product->id;?>" value="<?=$product->percentage_defect;?>"/>
                                    <input type="hidden" class="product-amount-defect-<?=$product->id;?>" value="<?=$product->amount_defect;?>"/>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Укажите процент продукта для проверки:
                                            <input name="NoticeControl[product_percentage][<?=$product->id;?>]" value="<?=$product->percentage;?>" type="number" min="1" max="100" data="<?=$product->id;?>" class="form-control product-check-percent product-check-percent-<?=$product->id;?>">
                                            <span class="percentage-error-<?=$product->id;?>" style="color:#cc0000; display:none;">Процент не должен быть меньше <?=$product->percentage;?>%</span>
                                        </div>
                                        <div class="col-sm-6">
                                            Укажите кол-во проверяемого продукта:
                                            <input name="NoticeControl[product_amount][<?=$product->id;?>]" value="<?=$product->amount_product;?>" type="text" class="form-control product-check product-check-<?=$product->id;?>" data="<?=$product->id;?>">
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Укажите процент дефектной продукции:
                                            <input name="NoticeControl[product_percentage_defect][<?=$product->id;?>]" value="<?=$product->percentage_defect;?>" type="number" data="<?=$product->id;?>" class="form-control product-defect-percent product-defect-percent-<?=$product->id;?>">
                                            <span class="percentage-defect-error-<?=$product->id;?>" style="color:#cc0000; display:none;">Процент не должен быть меньше <?=$product->percentage_defect;?>%</span>
                                        </div>
                                        <div class="col-sm-6">
                                            Укажите кол-во дефектной продукции:
                                            <input name="NoticeControl[defects][<?=$product->id;?>]" type="text" value="<?=$product->amount_defect;?>" data="<?=$product->id;?>" class="form-control product-defect product-defect-<?=$product->id;?>">
                                        </div>
                                    </div>
                                    <br/>
                                    Укажите причину:
                                    <textarea name="NoticeControl[reason][<?=$product->id;?>]" class="form-control" rows="8"><?=$product->reason;?></textarea>
                                </div>
                            </div>
                        <?php }?>
                        <?php if ($model->status != 1) {?>
                            <div id="form-button">
                                <?=Html::submitButton('<i class="fa fa-check"></i> Готово', ['name'=>'NoticeControl[button]', 'value'=>'ok', 'class'=>'btn btn-success']);?>
                                <?=Html::submitButton('<i class="fa fa-exclamation-circle"></i> NG', ['name'=>'NoticeControl[button]', 'value'=>'ng', 'class'=>'btn btn-danger']);?>
                            </div>
                        <?php }?>
                    <?php ActiveForm::end();?>
                </div>
            <?php }?>
        <?php } else {?>
            <div class="alert alert-warning text-center">Данных нет</div>
        <?php }?>
    </section>
</div>

<?php
$script = <<<JS
    $(document).on('input', '.product-check', function(event) {
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }

        var id = $(this).attr('data');
        var percentage = $('.product-percentage-'+id).val();
        var status = $('#control-status').val();

        $('.percentage-error-'+id).hide();
        $('#form-button').show();

        var amount = $(this).val()*100/$('.product-amount-'+id).val();

        if (percentage && (status == '2')) {
            if (amount < percentage) {
                $('.percentage-error-'+id).show();
                $('#form-button').hide();
            }
        }

        $('.product-check-percent-'+id).val(amount);
    });

    $(document).on('input', '.product-check-percent', function() {
        var id = $(this).attr('data');
        var percentage = $('.product-percentage-'+id).val();
        var status = $('#control-status').val();

        $('.percentage-error-'+id).hide();
        $('#form-button').show();

        if ($(this).val() > 100) {
            $(this).val('100');
        }

        if (percentage && (status == '2')) {
            if (parseInt($(this).val()) < parseInt(percentage)) {
                $('.percentage-error-'+id).show();
                $('#form-button').hide();
            }
        } else {
            if ($(this).val() < 0) {
                $(this).val('');
            }
        }
        var amount = $('.product-amount-'+id).val()/100*$(this).val();

        $('.product-check-'+id).val(amount);
    });

    $(document).on('input', '.product-defect', function() {
        var id = $(this).attr('data');

        var percentage = $('.product-percentage-defect-'+id).val();
        var status = $('#control-status').val();

        var amount = $(this).val()*100/$('.product-check-'+id).val();

        $('.product-defect-percent-'+id).val(amount);
    });

    $(document).on('input', '.product-defect-percent', function() {
        var id = $(this).attr('data');
        var percentage = $('.product-percentage-defect-'+id).val();
        var status = $('#control-status').val();

        if ($(this).val() > 100) {
            $(this).val('100');
        }
        
        if ($(this).val() < 0) {
            $(this).val('');
        }

        var amount = $('.product-check-'+id).val()/100*$(this).val();

        $('.product-defect-'+id).val(amount);
    });

    $(document).on('click', '#product-edit', function() {
        $('#product-form').toggle();
    });
JS;

$this->registerJs($script);
?>