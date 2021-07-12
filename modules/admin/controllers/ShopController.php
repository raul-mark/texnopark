<?php
namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;

use app\models\user\User;

class ShopController extends Controller{
	public $user;

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/admin']);
        }
        $this->user = User::find()->with('moderatorAccess', 'moderatorAccess.moderator')->where(['id'=>Yii::$app->user->identity->id])->one();

        if (($this->user->role == User::ROLE_MODERATOR)) {
            $accesses = array();

            if ($this->user && $this->user->moderatorAccess) {
                foreach ($this->user->moderatorAccess as $v) {
                    if ($v && $v->moderator) {
                        $accesses[] = $v->moderator->url;
                    }
                }
            }

            if (!in_array('shop', $accesses)) {
                throw new HttpException(403, 'В доступе отказано');
            }
        }

        return parent::beforeAction($action);
    }

    public function actionIndex() {
        return $this->render('index');
    }
}