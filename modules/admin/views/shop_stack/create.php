<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShopStack */

$this->title = 'Create Shop Stack';
$this->params['breadcrumbs'][] = ['label' => 'Shop Stacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-stack-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
