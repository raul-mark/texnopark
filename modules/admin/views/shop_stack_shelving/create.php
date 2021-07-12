<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShopStackShelving */

$this->title = 'Create Shop Stack Shelving';
$this->params['breadcrumbs'][] = ['label' => 'Shop Stack Shelvings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-stack-shelving-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
