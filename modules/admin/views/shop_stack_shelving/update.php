<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShopStackShelving */

$this->title = 'Update Shop Stack Shelving: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shop Stack Shelvings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-stack-shelving-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
