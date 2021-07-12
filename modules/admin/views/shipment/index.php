<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;

$this->title = 'Отгрузка';
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
        <?php if (Yii::$app->session->hasFlash('shipment_removed')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('shipment_removed');?>
            </div>
        <?php }?>
        <div class="box">

            <div class="box-body" id="item-block">
                <div class="alert alert-danger text-center">Раздел на стадии разработки</div>
            </div>
        </div>
    </section>
</div>  