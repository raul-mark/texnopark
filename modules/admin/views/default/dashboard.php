<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\models\user\User;

$this->title = 'Админ панель';
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
        <?php if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {?>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?=($stocks && ($stocks > 0)) ? $stocks : 0;?></h3>
                            <p>Склады</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-archive"></i>
                        </div>
                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/stock'])?>" class="small-box-footer">Посмотреть <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?=($products && ($products > 0)) ? $products : 0;?></h3>
                            <p>Товары</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/product'])?>" class="small-box-footer">Посмотреть <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>0</h3>
                            <p>Магазин</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/shop'])?>" class="small-box-footer">Посмотреть <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>0</h3>
                            <p>Отдел ГП</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/gp'])?>" class="small-box-footer">Посмотреть <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        <?php }?>
    </section>
</div>