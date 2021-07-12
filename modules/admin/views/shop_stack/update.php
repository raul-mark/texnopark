<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShopStack */

$this->title = 'Update Shop Stack: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shop Stacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-stack-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
