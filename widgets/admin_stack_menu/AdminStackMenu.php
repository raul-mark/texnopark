<?php 
namespace app\widgets\admin_stack_menu;

use Yii;
use yii\bootstrap\Widget;

use app\models\shop\ShopStack;

class AdminStackMenu extends Widget{
	public function init() {}

	public function run() {
		$user = Yii::$app->user->identity;

		$model = ShopStack::find()->where(['id'=>Yii::$app->request->get('stack_id')])->one();

		return $this->render('admin-stack-menu', [
			'model' => $model,
			'user' => $user
		]);
	}
}
?>