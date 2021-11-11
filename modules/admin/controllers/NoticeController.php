<?php
namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;

use app\models\user\User;
use app\models\product\Product;
use app\models\Category;
use app\models\Notification;
use app\models\notice\waybill\NoticeWaybill;
use app\models\notice\waybill\NoticeWaybillSearch;
use app\models\notice\waybill\NoticeWaybillProduct;
use app\models\notice\waybill\NoticeWaybillProductSearch;
use app\models\notice\truck\NoticeTruck;
use app\models\notice\truck\NoticeTruckSearch;
use app\models\notice\truck\NoticeTruckProduct;
use app\models\notice\truck\NoticeTruckProductSearch;
use app\models\notice\control\NoticeControl;
use app\models\notice\control\NoticeControlSearch;
use app\models\notice\control\NoticeControlProduct;
use app\models\notice\control\NoticeControlProductSearch;
use app\models\notice\act\NoticeAct;
use app\models\notice\act\NoticeActSearch;
use app\models\notice\act\NoticeActProduct;
use app\models\notice\act\NoticeActProductSearch;
use app\models\stock\Stock;
use app\models\stack\Stack;
use app\models\stack\StackShelving;
use app\models\gp\DepartmentGp;
use app\models\gp\DepartmentForming;
use app\models\gp\DepartmentPlastic;
use app\models\gp\DepartmentRegulator;
use app\models\gp\Gp;

class NoticeController extends Controller{
	public $user;

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/worker']);
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

            if (!in_array('notice', $accesses)) {
                throw new HttpException(403, 'В доступе отказано');
            }
        }

        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $searchModel = new NoticeWaybillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('provider', 'noticeTruck', 'noticeTruck.noticeControl', 'noticeTruck.noticeControl.noticeAct');

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionWaybill($id) {
        $model = NoticeWaybill::find()->with('provider', 'noticeTruck', 'noticeTruck.noticeControl', 'noticeTruck.noticeControl.noticeAct')->where(['id'=>$id])->one();

        $searchModel = new NoticeWaybillProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_waybill_id' => $id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $notification = Notification::findOne(['object_id'=>$id, 'type'=>'truck']);
        if ($notification) {
            $notification->status_admin = 1;
            $notification->save(false);
        }

        return $this->render('waybill', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionTruck($id = null) {
        $model = NoticeTruck::find()->with('noticeWaybill', 'noticeWaybill.provider', 'noticeControl', 'noticeControl.noticeAct')->where(['id'=>$id])->one();

        $searchModel = new NoticeTruckProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_truck_id' => $id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $truck = NoticeTruck::findOne($id);

        if ($truck->load(Yii::$app->request->post()) && $truck->validate()) {
            if ($truck->saveObject()) {
                Yii::$app->session->setFlash('truck_notice_saved', 'Заявка успешно подтверждена');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        $notification = Notification::findOne(['object_id'=>$id, 'type'=>'control']);
        if ($notification) {
            $notification->status_admin = 1;
            $notification->save(false);
        }

        return $this->render('truck', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'truck' => $truck
        ]);
    }

    public function actionControl($id = null) {
        $model = NoticeControl::find()->with('noticeTruck', 'noticeTruck.noticeWaybill', 'noticeTruck.noticeWaybill.provider', 'noticeAct')->where(['id'=>$id])->one();

        $searchModel = new NoticeControlProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_control_id' => $id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $notification = Notification::findOne(['object_id'=>$id, 'type'=>'act']);
        if ($notification) {
            $notification->status_admin = 1;
            $notification->save(false);
        }

        return $this->render('control', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAct($id = null) {
        $model = NoticeAct::find()->with('noticeControl', 'noticeControl.noticeTruck', 'noticeControl.noticeTruck.noticeWaybill', 'noticeControl.noticeTruck.noticeWaybill.provider')->where(['id'=>$id])->one();

        $searchModel = new NoticeActProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_act_id' => $id])->andWhere(['status'=>1]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $notification = Notification::findOne(['object_id'=>$id, 'type'=>'notice_finish']);
        if ($notification) {
            $notification->status_admin = 1;
            $notification->save(false);
        }

        return $this->render('act', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionRemove($id) {
        $model = NoticeWaybill::find()->with('noticeTruck', 'noticeTruck.noticeControl', 'noticeTruck.noticeControl.noticeAct')->where(['id'=>$id])->one();
        if ($model) {
            $ids[] = ['table'=>'waybill', 'id'=>$model->id];

            if ($model->noticeTruck) {
                $ids[] = ['table'=>'truck', 'id'=>$model->noticeTruck->id];
            }

            if ($model->noticeTruck->noticeControl) {
                $ids[] = ['table'=>'control', 'id'=>$model->noticeTruck->noticeControl->id];
            }

            if ($model->noticeTruck->noticeControl->noticeAct) {
                $ids[] = ['table'=>'act', 'id'=>$model->noticeTruck->noticeControl->noticeAct->id];
                $ids[] = ['table'=>'notice_finish', 'id'=>$model->noticeTruck->noticeControl->noticeAct->id];
            }

            foreach ($ids as $k => $v) {
                $notification = Notification::find()->where(['type'=>$v['table']])->andWhere(['object_id'=>$v['id']])->one();
                if ($notification) {
                    $notification->delete();
                }
            }

            $model->delete();
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    // update
    public function actionWaybillUpdate($id) {
        $model = NoticeWaybill::find()->with('noticeWaybillProducts')->where(['id'=>$id])->one();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->saveObject('update')) {
                Yii::$app->session->setFlash('waybill_saved', 'Накладная успешно сохранена');
                return $this->redirect(['/admin/notice/waybill', 'id'=>$model->id]);
            }
        }

        $providers = ArrayHelper::map(Category::find()->where(['type'=>'provider'])->all(), 'id', 'name_ru');
        $products = ArrayHelper::map(Product::find()->all(), 'id', 'name_ru');
        $articles = ArrayHelper::map(Product::find()->all(), 'id', 'article');
        $units = ArrayHelper::map(Category::find()->where(['type'=>'unit'])->all(), 'id', 'name_ru');

        return $this->render('update/waybill', [
            'model' => $model,
            'providers' => $providers,
            'products' => $products,
            'articles' => $articles,
            'units' => $units
        ]);
    }

    public function actionTruckUpdate($id) {
        $model = NoticeTruck::find()->with('noticeWaybill', 'noticeWaybill.provider')->where(['id'=>$id])->one();

        $searchModel = new NoticeTruckProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_truck_id' => $id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $products = NoticeTruckProduct::find()->with('product', 'unit')->where(['notice_truck_id'=>$id])->all();

        $truck = NoticeTruck::findOne($id);

        if ($truck->load(Yii::$app->request->post()) && $truck->validate()) {
            if ($truck->saveObject('update')) {
                Yii::$app->session->setFlash('truck_saved', 'Заявка успешно подтверждена');
                return $this->redirect(['/admin/notice/truck', 'id'=>$model->id]);
            }
        }

        return $this->render('update/truck', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'truck' => $truck,
            'products' => $products
        ]);
    }

    public function actionControlUpdate($id) {
        $model = NoticeControl::find()->with('noticeTruck', 'noticeTruck.noticeWaybill', 'noticeTruck.noticeWaybill.provider')->where(['id'=>$id])->one();

        $searchModel = new NoticeControlProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_control_id' => $id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        $products = NoticeControlProduct::find()->with('product')->where(['notice_control_id'=>$id])->all();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveObject('update')) {
                Yii::$app->session->setFlash('control_notice_accepted', 'Данные успешно сохранены');
                return $this->redirect(['/admin/notice/control', 'id'=>$model->id]);
            }
        }

        return $this->render('update/control', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => $products
        ]);
    }

    public function actionActUpdate($id) {
        $model = NoticeAct::find()->with('noticeControl', 'noticeControl.noticeTruck', 'noticeControl.noticeTruck.noticeWaybill', 'noticeControl.noticeTruck.noticeWaybill.provider')->where(['id'=>$id])->one();

        $searchModel = new NoticeActProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with('product', 'unit')->andWhere(['notice_act_id' => $id])->andWhere(['status'=>1]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => 'desc'
            ]
        ]);

        return $this->render('update/act', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionActAccept($id) {
        $model = NoticeAct::find()->with('noticeActProducts')->where(['id'=>$id])->one();

        if ($model) {
            $model->status = 1;
            $model->save(false);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionActDecline($id) {
        $model = NoticeAct::find()->with('noticeActProducts')->where(['id'=>$id])->one();

        if ($model) {
            $model->status = 0;
            $model->save(false);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAcceptIndustry($id, $department_id, $type) {
        switch($type) {
            case 'b_department_forming' : $model = DepartmentForming::findOne(['id'=>$id]); break;
            case 'b_department_regulator' : $model = DepartmentRegulator::findOne(['id'=>$id]); break;
            case 'b_department_plastic' : $model = DepartmentPlastic::findOne(['id'=>$id]); break;
            case 'b_department_gp' : $model = DepartmentGp::findOne(['id'=>$id]); break;
        }

        if ($model) {
            $gp = new Gp;
            $gp->name_ru = $model->name_ru;
            $gp->article = $model->article;
            $gp->amount = $model->amount;
            $gp->save(false);

            Yii::$app->session->setFlash('gp_industry_saved', 'Продукция успешно добавлена');
            return $this->redirect(['/admin/gp/view', 'id'=>$gp->id]);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}