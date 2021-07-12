<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use app\models\user\User;
?>

<?php if (($user->role == User::ROLE_ADMIN) || ($user->role == User::ROLE_MODERATOR)) {?>
    <div class="box">
        <div class="box-body">
            <div class="text-center">
                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/shipment/view', 'id'=>$model->id])?>">
                    <img class="profile-user-img img-responsive" src="/admin_files/img/dashboard/box.png" width="70%">
                </a>
            </div>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/shipment/create', 'id'=>$model->id])?>" class="btn btn-primary width-full">Редактировать</a></li>
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/shipment/remove', 'id'=>$model->id])?>" class="btn btn-danger width-full remove-object">Удалить</a></li>
            </ul>
        </div>
    </div>
<?php }?>

<?php if ($user->role == User::ROLE_STOCK) {?>
    <div class="box">
        <div class="box-body">
            <div class="text-center">
                <a href="<?=Yii::$app->urlManager->createUrl(['/stock/shipment/view', 'id'=>$model->id])?>">
                    <img class="profile-user-img img-responsive" src="/admin_files/img/dashboard/box.png" width="70%">
                </a>
            </div>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/stock/shipment/create', 'id'=>$model->id])?>" class="btn btn-primary width-full">Редактировать</a></li>
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/stock/shipment/remove', 'id'=>$model->id])?>" class="btn btn-danger width-full remove-object">Удалить</a></li>
            </ul>
        </div>
    </div>
<?php }?>

<?php if ($user->role == User::ROLE_TO) {?>
    <div class="box">
        <div class="box-body">
            <div class="text-center">
                <a href="<?=Yii::$app->urlManager->createUrl(['/maintenance/shipment/view', 'id'=>$model->id])?>">
                    <img class="profile-user-img img-responsive" src="/admin_files/img/dashboard/box.png" width="70%">
                </a>
            </div>
            <ul class="company-left-menu">
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/maintenance/shipment/create', 'id'=>$model->id])?>" class="btn btn-primary width-full">Редактировать</a>
                <li><a href="<?=Yii::$app->urlManager->createUrl(['/maintenance/shipment/remove', 'id'=>$model->id])?>" class="btn btn-danger width-full remove-object">Удалить</a>
            </ul>
        </div>
    </div>
<?php }?>

