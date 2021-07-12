<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopStackShelvingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shop Stack Shelvings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-stack-shelving-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shop Stack Shelving', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'shop_stack_id',
            'row',
            'cell',
            'status',
            //'sort',
            //'date',
            //'ip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
