<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use app\models\user\User;
?>

<?php if (($model->role == User::ROLE_ADMIN) || ($model->role == User::ROLE_MODERATOR)) {?>
    <div class="box box-info color-palette-box">
        <div class="box-body">
            <img src="<?=$model->getPhoto('250x250');?>" width="100%"/>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/default/update'])?>" class="btn btn-primary width-full">Редактировать</a></li>
            </ul>
        </div>
    </div>
<?php }?>

<?php if ($model->role == User::ROLE_WORKER) {?>
    <div class="box box-info color-palette-box">
        <div class="box-body">
            <img src="<?=$model->getPhoto('250x250');?>" width="100%"/>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker/default/update'])?>" class="btn btn-primary width-full">Редактировать</a></li>
            </ul>
        </div>
    </div>
<?php }?>

<?php if ($model->role == User::ROLE_WORKER_SHOP) {?>
    <div class="box box-info color-palette-box">
        <div class="box-body">
            <img src="<?=$model->getPhoto('250x250');?>" width="100%"/>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/default/update'])?>" class="btn btn-primary width-full">Редактировать</a></li>
            </ul>
        </div>
    </div>
<?php }?>