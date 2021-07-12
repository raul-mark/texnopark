<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = 'Магазин';
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
        <?php if (Yii::$app->session->hasFlash('news_removed')) {?>
            <div class="callout callout-success text-center">
                <?=Yii::$app->session->getFlash('news_removed');?>
            </div>
        <?php }?>
        <div class="box box-info color-palette-box">
            <div class="box-header with-border">
                <div id="action-links" style="display:none">
                    <a href="javascript:;" class="btn btn-danger" data-value="remove"><i class="fa fa-trash"></i> Удалить</a>
                    <a href="javascript:;" class="btn btn-warning" data-value="disable"><i class="fa fa-lock"></i> Заблокировать</a>
                    <a href="javascript:;" class="btn btn-success" data-value="enable"><i class="fa fa-unlock"></i> Разблокировать</a>
                </div>
            </div>
            <div class="box-body" id="item-block">
                <div class="alert alert-warning text-center"><i class="fa fa-exclamation-triangle"></i> Раздел в рзработке</div>
            </div>
        </div>
    </section>
</div>