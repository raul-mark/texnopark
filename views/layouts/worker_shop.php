<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AdminAsset;
use yii\bootstrap\ActiveForm;

use app\models\user\User;
use app\models\Notification;
use app\models\Category;
use app\models\shipment\Shipment;
use app\models\Settings;
use app\models\notice\truck\NoticeTruck;
use app\models\notice\control\NoticeControl;
use app\models\notice\act\NoticeAct;

AdminAsset::register($this);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$user = null;
if (!Yii::$app->user->isGuest) {
    $user = User::find()->with('image', 'moderatorAccess', 'moderatorAccess.moderator')->where(['id'=>Yii::$app->user->identity->id])->one();
    $notifications = Notification::find()->where(['user_id'=>$user->id, 'status'=>0])->all();
}

$accesses = array();

if ($user && $user->moderatorAccess) {
    foreach ($user->moderatorAccess as $v) {
        if ($v && $v->moderator) {
            $accesses[] = $v->moderator->url;
        }
    }
}

$shipments = Shipment::find()->where(['status'=>0])->count();
$trucks = NoticeTruck::find()->where(['status'=>0])->count();
$controls = NoticeControl::find()->where(['status'=>0])->count();
$acts = NoticeAct::find()->where(['status'=>0])->count();

$currency = Settings::findOne(['type'=>'currency']);
if (!$currency) {
    $currency = new Settings;
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>

    <div class="wrapper">
        <?php if (($controller != 'default') || (($controller == 'default') && ($action != 'index'))) {?>
            <header class="main-header">
                <a href="<?=Yii::$app->urlManager->createUrl(['/'])?>" class="logo">
                    <span class="logo-mini"><img src="/assets_files/img/texno_logo.png" width="50"/></span>
                    <span class="logo-lg"><img src="/assets_files/img/texno_logo.png" width="50"/></span>
                </a>
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning"><?=($notifications && count($notifications) > 0) ? count($notifications) : '';?></span>
                                </a>
                                <?php if ($notifications) {?>
                                    <ul class="dropdown-menu">
                                        <li class="header">У вас <?=count($notifications);?> уведомлений</li>
                                        <li>
                                            <ul class="menu">
                                                <?php foreach ($notifications as $k => $v) {?>
                                                    <li>
                                                        <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/order/view', 'id'=>$v->object_id]);?>">
                                                            <i class="fa fa-shopping-cart text-green"></i> <?=$v->message;?>
                                                        </a>
                                                    </li>
                                                <?php }?>
                                            </ul>
                                        </li>
                                        <li class="footer"><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/notification']);?>">Смотреть все</a></li>
                                    </ul>
                                <?php }?>
                            </li>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?=$user ? $user->getPhoto('100x100') : '';?>" class="user-image" alt="User Image">
                                    <span class="hidden-xs">Мой профиль</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?=$user ? $user->getPhoto('100x100') : '';?>" class="img-circle" alt="User Image">
                                        <p>Мой профиль<br/><?=$user ? $user->name : '';?></p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/default/profile'])?>" class="btn btn-default btn-flat">Информация</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?=Yii::$app->urlManager->createUrl(['/main/log-out'])?>" class="btn btn-default btn-flat">Выйти</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            
                        </ul>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar">
                <section class="sidebar">
                    <div class="user-panel" style="height:70px">
                        <div class="pull-left info">
                            <p><?=$user ? $user->name : '';?></p>
                            <a href="javascript:;"><i class="fa fa-circle text-success"></i> Администратор</a>
                        </div>
                    </div>
                    
                    <!-- <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Поиск...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form> -->

                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">Меню</li>

                        <li <?=(($controller == 'default') && ($action == 'dashboard')) ? 'class="active"' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/default/dashboard'])?>">
                                <i class="fa fa-home"></i> <span>Главная</span>
                            </a>
                        </li>

                        
                        <li <?=($controller == 'stock') ? 'class="active"' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/stock'])?>">
                                <i class="fa fa-archive"></i> <span>Склад</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-<?=($stock_count > 0) ? 'green' : 'red'?>"><?=$stock_count;?></small>
                                </span>
                            </a>
                        </li>
                    
                        <li <?=($controller == 'shop') ? 'class="active"' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/shop'])?>">
                                <i class="fa fa-shopping-cart"></i> <span>Магазин</span>
                            </a>
                        </li>
                    
                        <li <?=($controller == 'gp') ? 'class="active"' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/gp'])?>">
                                <i class="fa fa-star"></i> <span>Отдел ГП</span>
                            </a>
                        </li>
                    
                        <li class="treeview<?=($controller == 'product') ? ' active' : '';?>">
                            <a href="#">
                                <i class="fa fa-laptop"></i> <span>Продукция</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/product/'])?>"><i class="fa fa-circle-o"></i> Список</a></li>
                                <li><a href="<?=Yii::$app->urlManager->createUrl(['/worker_shop/product/', 'type'=>'defect'])?>"><i class="fa fa-circle-o"></i> Дефектная продукция</a></li>
                            </ul>
                        </li>
                        
                        <li>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/main/log-out'])?>">
                                <i class="fa fa-sign-out"></i>
                                <span>Выйти</span>
                            </a>
                        </li>
                    </ul>
                </section>
            </aside>
        <?php }?>

        <?=$content;?>

        <?php if (($controller != 'default') || (($controller == 'default') && ($action != 'index'))) {?>
            <footer class="main-footer text-center">
                <?=date('Y');?> Texnopark
            </footer>
            <div class="control-sidebar-bg"></div>
        <?php }?>
    </div>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>