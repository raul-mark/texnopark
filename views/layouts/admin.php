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
use app\models\notice_shop_stock\NoticeShopStock;
use app\models\Settings;

AdminAsset::register($this);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$user = null;
if (!Yii::$app->user->isGuest) {
    $user = User::find()->with('image', 'moderatorAccess', 'moderatorAccess.moderator')->where(['id'=>Yii::$app->user->identity->id])->one();
    $notifications = Notification::find()->where(['status_admin'=>0])->all();
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
$notice_shop = NoticeShopStock::find()->where(['status'=>0])->count();

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
<body class="hold-transition <?=(($controller == 'default') && ($action == 'index')) ? 'login-page' : 'skin-blue sidebar-mini'?>">
<?php $this->beginBody() ?>

    <div class="wrapper">
        <?php if (($controller != 'default') || (($controller == 'default') && ($action != 'index'))) {?>
            <header class="main-header">
                <a href="<?=Yii::$app->urlManager->createUrl(['/'])?>" class="logo">
                    <!-- <span class="logo-mini"><img src="/assets_files/img/texno_logo.png" width="50"/></span>
                    <span class="logo-lg"><img src="/assets_files/img/texno_logo.png" width="50"/></span> -->
                    <span class="logo-mini"><b>T</b>P</span>
                    <span class="logo-lg"><b>TEXNO</b>PARK</span>
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
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notification']);?>">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning"><?=($notifications && count($notifications) > 0) ? count($notifications) : '';?></span>
                                </a>
                                <?php if ($notifications) {?>
                                    <ul class="dropdown-menu">
                                        <li class="header">?? ?????? <?=count($notifications);?> ??????????????????????</li>
                                        <li>
                                            <ul class="menu">
                                                <?php foreach ($notifications as $k => $v) {?>
                                                    <li>
                                                        <a href="<?=Yii::$app->urlManager->createUrl(['/admin/order/view', 'id'=>$v->object_id]);?>">
                                                            <i class="fa fa-shopping-cart text-green"></i> <?=$v->message;?>
                                                        </a>
                                                    </li>
                                                <?php }?>
                                            </ul>
                                        </li>
                                        <li class="footer"><a href="<?=Yii::$app->urlManager->createUrl(['/admin/notification']);?>">???????????????? ??????</a></li>
                                    </ul>
                                <?php }?>
                            </li>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?=$user ? $user->getPhoto('100x100') : '';?>" class="user-image" alt="User Image">
                                    <span class="hidden-xs">?????? ??????????????</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?=$user ? $user->getPhoto('100x100') : '';?>" class="img-circle" alt="User Image">
                                        <p>?????? ??????????????<br/><?=$user ? $user->name : '';?></p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?=Yii::$app->urlManager->createUrl(['/admin/default/profile'])?>" class="btn btn-default btn-flat">????????????????????</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?=Yii::$app->urlManager->createUrl(['/main/log-out'])?>" class="btn btn-default btn-flat">??????????</a>
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
                            <a href="javascript:;"><i class="fa fa-circle text-success"></i> ??????????????????????????</a>
                        </div>
                    </div>
                    
                    <!-- <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="??????????...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form> -->

                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">????????</li>

                        <li <?=(($controller == 'default') && ($action == 'dashboard')) ? 'class="active"' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/admin/default/dashboard'])?>">
                                <i class="fa fa-home"></i> <span>??????????????</span>
                            </a>
                        </li>

                        <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('stock', $accesses))) {?>
                            <li <?=($controller == 'stock') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/stock'])?>">
                                    <i class="fa fa-archive"></i> <span>????????</span>
                                    <span class="pull-right-container">
                                        <small class="label pull-right bg-<?=($stock_count > 0) ? 'green' : 'red'?>"><?=$stock_count;?></small>
                                    </span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('shop', $accesses))) {?>
                            <li <?=($controller == 'shop') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/shop/stacks'])?>">
                                    <i class="fa fa-shopping-cart"></i> <span>??????????????</span>
                                    <span class="pull-right-container">
                                        <small class="label pull-right bg-red"><?=$shipments;?></small>
                                    </span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if ($user && ($user->role == User::ROLE_ADMIN) || in_array('product', $accesses)) {?>
                            <li <?=(($controller == 'product') && !Yii::$app->request->get('type')) ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/product'])?>">
                                    <i class="fa fa-laptop"></i> <span>??????????????????</span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if($user && (($user->role == User::ROLE_ADMIN))) {?>
                            <li <?=($controller == 'notice') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/notice'])?>">
                                    <i class="fa fa-check"></i> <span>???????????? (??????????)</span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if($user && (($user->role == User::ROLE_ADMIN))) {?>
                            <li <?=($controller == 'shipment') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/shipment'])?>">
                                    <i class="fa fa-arrow-left"></i>
                                    <span>???????????? ???? ????????????????</span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('gp', $accesses))) {?>
                            <li <?=($controller == 'gp') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/gp'])?>">
                                    <i class="fa fa-star"></i> <span>?????????? ????</span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if ($user && ($user->role == User::ROLE_ADMIN) || in_array('product?type=defect', $accesses)) {?>
                            <li <?=(($controller == 'product') && (Yii::$app->request->get('type') == 'defect')) ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/product', 'type'=>'defect'])?>">
                                    <i class="fa fa-remove"></i> <span>?????????????????? ??????????????????</span>
                                </a>
                            </li>
                        <?php }?>

                        <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('worker', $accesses))) {?>
                            <li class="treeview<?=(($controller == 'worker') || ($controller == 'worker-shop')) ? ' active' : '';?>">
                                <a href="#">
                                    <i class="fa fa-group"></i> <span>????????????????????</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('worker', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/worker/'])?>"><i class="fa fa-circle-o"></i> ??????????</a></li>
                                    <?php }?>
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('worker-shop', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/worker-shop/'])?>"><i class="fa fa-circle-o"></i> ??????????????</a></li>
                                    <?php }?>
                                </ul>
                            </li>
                        <?php }?>

                        <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('category?type=category', $accesses) || in_array('category?type=provider', $accesses) || in_array('category?type=unit', $accesses) || in_array('category?type=manufacturer', $accesses) || in_array('category?type=worker', $accesses) || in_array('category?type=defect', $accesses))) {?>
                            <li class="treeview<?=($controller == 'category') ? ' active' : '';?>">
                                <a href="#">
                                    <i class="fa fa-list"></i> <span>????????????????????</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('category?type=category', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/category/', 'type'=>'category'])?>"><i class="fa fa-circle-o"></i> ?????????????????? ??????????????</a></li>
                                    <?php }?>
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('category?type=provider', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/category/', 'type'=>'provider'])?>"><i class="fa fa-circle-o"></i> ????????????????????</a></li>
                                    <?php }?>
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('category?type=unit', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/category/', 'type'=>'unit'])?>"><i class="fa fa-circle-o"></i> ????. ??????????????????</a></li>
                                    <?php }?>
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('category?type=manufacturer', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/category/', 'type'=>'manufacturer'])?>"><i class="fa fa-circle-o"></i> ??????????????????????????</a></li>
                                    <?php }?>
                                    
                                    <?php if ($user->role == User::ROLE_ADMIN || in_array('category?type=defect', $accesses)) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/category/', 'type'=>'defect'])?>"><i class="fa fa-circle-o"></i> ???????? ????????????????</a></li>
                                    <?php }?>
                                </ul>
                            </li>
                        <?php }?>

                        <!-- <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('settings/contacts', $accesses) || in_array('settings/logo', $accesses))) {?>
                            <?php $admin_settings = ['settings'];?>
                            <li class="treeview<?=in_array($controller, $admin_settings) ? ' active' : '';?>">
                                <a href="#">
                                    <i class="fa fa-cogs"></i> <span>??????????????????</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('settings/contacts', $accesses))) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/settings/contacts'])?>"><i class="fa fa-circle-o"></i> ????????????????</a></li>
                                    <?php }?>
                                    <?php if ($user && (($user->role == User::ROLE_ADMIN) || in_array('settings/logo', $accesses))) {?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['/admin/settings/logo'])?>"><i class="fa fa-circle-o"></i> ??????????????</a></li>
                                    <?php }?>
                                </ul>
                            </li>
                        <?php }?> -->

                        <?php if($user && (($user->role == User::ROLE_ADMIN))) {?>
                            <li <?=($controller == 'report') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/report'])?>">
                                    <i class="fa fa-file"></i> <span>????????????????????</span>
                                </a>
                            </li>
                        <?php }?>
                        
                        <?php if($user && (($user->role == User::ROLE_ADMIN))) {?>
                            <li <?=($controller == 'moderator') ? 'class="active"' : '';?>>
                                <a href="<?=Yii::$app->urlManager->createUrl(['/admin/moderator'])?>">
                                    <i class="fa fa-user-secret"></i> <span>????????????????????</span>
                                </a>
                            </li>
                        <?php }?>

                        <!-- <li <?=(($controller == 'default') && (($action == 'change-password') || ($action == 'profile'))) ? ' active' : '';?>>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/admin/default/profile'])?>">
                                <i class="fa fa-pencil"></i> <span>?????? ??????????????</span>
                            </a>
                        </li> -->
                        
                        <li>
                            <a href="<?=Yii::$app->urlManager->createUrl(['/main/log-out'])?>">
                                <i class="fa fa-sign-out"></i>
                                <span>??????????</span>
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