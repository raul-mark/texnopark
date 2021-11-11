<?php

namespace app\models\notice\control;

use Yii;
use app\models\user\User;
use app\models\product\Product;
use app\models\notice\truck\NoticeTruck;
use app\models\notice\act\NoticeAct;
use app\models\Category;
use app\models\Notification;

/**
 * This is the model class for table "notice_control".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $notice_truck_id
 * @property string|null $date_notice
 * @property string|null $description
 * @property string $date
 * @property int $sort
 * @property int $status
 *
 * @property NoticeAct[] $noticeActs
 * @property NoticeTruck $noticeTruck
 * @property User $user
 * @property NoticeControlProduct[] $noticeControlProducts
 */
class NoticeControl extends \yii\db\ActiveRecord
{
    public $truck_number, $truck_number_reg, $invoice_number, $provider_id, $article, $description;
    public $product_percentage;
    public $defects;
    public $product_percentage_defect;
    public $product_amount;
    public $reason;
    public $button;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notice_control';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'notice_truck_id', 'sort', 'status'], 'integer'],
            [['description'], 'string'],
            [['date', 'product_percentage', 'defects', 'reason', 'product_percentage_defect', 'product_amount', 'button'], 'safe'],
            [['date_notice'], 'string', 'max' => 255],
            [['notice_truck_id'], 'exist', 'skipOnError' => true, 'targetClass' => NoticeTruck::className(), 'targetAttribute' => ['notice_truck_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'notice_truck_id' => 'Notice Truck ID',
            'date_notice' => 'Date Notice',
            'description' => 'Description',
            'date' => 'Date',
            'sort' => 'Sort',
            'status' => 'Status',
        ];
    }

    public function saveObject($type = 'create') {
        $post = Yii::$app->request->post();

        $this->user_id = Yii::$app->user->identity->id;
        $this->status = ($this->button == 'ok') ? 1 : 2;

        if ($this->save()) {
            if ($this->product_percentage) {
                foreach ($this->product_percentage as $key => $percentage) {
                    $pr = NoticeControlProduct::findOne($key);
                    if ($pr) {
                        $pr->percentage = $percentage;
                        $pr->amount_passed = $pr->amount - $this->defects[$key];
                        $pr->amount_defect = $this->defects[$key];
                        $pr->reason = $this->reason[$key];
                        $pr->amount_product = $this->product_amount[$key];
                        $pr->percentage_defect = $this->product_percentage_defect[$key];
                        $pr->save(false);
                    }
                }
            }

            if ($type == 'create') {
                if ($this->button == 'ok') {
                    $products = NoticeControlProduct::find()->where(['notice_control_id'=>$this->id, 'status'=>1])->all();

                    $act = new NoticeAct;
                    $act->date_notice = $this->date_notice;
                    $act->status = 0;
                    $act->notice_control_id = $this->id;

                    if ($act->save(false)) {
                        $notification = new Notification;
                        $notification->user_id = Yii::$app->user->identity->id;
                        $notification->object_id = $act->id;
                        $notification->status = 0;
                        $notification->status_admin = 0;
                        $notification->message = 'Новая заявка от раздела: контроль качества';
                        $notification->type = 'act';
                        $notification->save();

                        $keys = ['notice_act_id', 'product_id', 'unit_id', 'amount', 'percentage', 'amount_passed', 'amount_defect', 'description', 'reason', 'status', 'amount_product', 'percentage_defect'];
                        $vals = [];
                        if ($products) {
                            foreach ($products as $k => $product) {
                                $vals[] = [
                                    'notice_act_id' => $act->id,
                                    'product_id' => $product->product_id,
                                    'unit_id' => $product->unit_id,
                                    'amount' => $product->amount,
                                    'percentage' => $product->percentage,
                                    'amount_passed' => $product->amount_passed,
                                    'amount_defect' => $product->amount_defect,
                                    'description' => $product->description,
                                    'reason' => $product->reason,
                                    'status' => 1,
                                    'amount_product' => $product->amount_product,
                                    'percentage_defect' => $product->percentage_defect
                                ];
                            }

                            Yii::$app->db->createCommand()->batchInsert('notice_act_product', $keys, $vals)->execute();
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Gets query for [[NoticeActs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticeActs()
    {
        return $this->hasMany(NoticeAct::className(), ['notice_control_id' => 'id']);
    }

    /**
     * Gets query for [[NoticeTruck]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticeTruck()
    {
        return $this->hasOne(NoticeTruck::className(), ['id' => 'notice_truck_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[NoticeControlProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticeControlProducts()
    {
        return $this->hasMany(NoticeControlProduct::className(), ['notice_control_id' => 'id']);
    }

    public function getProvider()
    {
        return $this->hasOne(Category::className(), ['id' => 'provider_id']);
    }

    public function getNoticeAct() {
        return $this->hasOne(NoticeAct::className(), ['notice_control_id' => 'id']);
    }
}
