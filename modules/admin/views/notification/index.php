<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Уведомления';
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
        <?php if (Yii::$app->session->hasFlash('notification_removed')) {?>
            <div class="alert alert-success text-center">
                <?=Yii::$app->session->getFlash('notification_removed');?>
            </div>
        <?php }?>
        <div class="box box box-info color-palette-box">
            <div class="box-header">Список уведомлений</div>
            <div class="box-body">
                <?php if ($alerts) {?>
                    <?php foreach ($alerts as $key => $alert) {?>
                        <div class="alert alert-success">
                            <i class="fa fa-clock-o"></i> <?=$alert->date;?>
                            <br/>
                            <?=$alert->message;?>
                            <?php if (($alert->type == 'b_department_gp') || ($alert->type == 'b_department_forming') || ($alert->type == 'b_department_regulator') || ($alert->type == 'b_department_plastic')) {?>
                                <br/>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/gp/create', 'id'=>$alert->object_id, 'department_id'=>$alert->department_id, 'type' => $alert->type]);?>">Добавить</a>
                            <?php }?>
                            <?php if ($alert->type == 'truck') {?>
                                <br/>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/waybill', 'id'=>$alert->object_id]);?>">Посмотреть</a>
                            <?php }?>
                            <?php if ($alert->type == 'control') {?>
                                <br/>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/truck', 'id'=>$alert->object_id]);?>">Посмотреть</a>
                            <?php }?>
                            <?php if ($alert->type == 'act') {?>
                                <br/>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/control', 'id'=>$alert->object_id]);?>">Посмотреть</a>
                            <?php }?>
                            <?php if ($alert->type == 'notice_finish') {?>
                                <br/>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice/act', 'id'=>$alert->object_id]);?>">Посмотреть</a>
                            <?php }?>
                        </div>
                    <?php }?>
                    <?php if ($pagination) {?>
                        <?=LinkPager::widget(['pagination'=>$pagination]);?>
                    <?php }?>
                <?php } else {?>
                    <div class="alert alert-warning text-center">Уведомлений нет</div>
                <?php }?>
            </div>
        </div>
    </section>
</div>