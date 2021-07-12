<?php

namespace app\models\notice\waybill;

use Yii;
use app\models\user\User;
use app\models\product\Product;
use app\models\notice\truck\NoticeTruck;
use app\models\Category;

/**
 * This is the model class for table "notice_waybill".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $date_notice
 * @property string|null $truck_number
 * @property string|null $truck_number_reg
 * @property string|null $invoice_number
 * @property int|null $provider_id
 * @property string|null $article
 * @property string|null $description
 * @property string $date
 * @property int $sort
 * @property int $status
 *
 * @property NoticeTruck[] $noticeTrucks
 * @property User $user
 * @property NoticeWaybillProduct[] $noticeWaybillProducts
 */
class NoticeWaybill extends \yii\db\ActiveRecord
{
    public $password;
    public $products = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notice_waybill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['truck_number', 'truck_number_reg', 'date_notice'], 'required', 'message' => 'Заполните поле'],
            [['user_id', 'provider_id', 'sort', 'status'], 'integer'],
            [['description'], 'string'],
            [['date', 'products'], 'safe'],
            [['date_notice', 'truck_number', 'truck_number_reg', 'invoice_number', 'article'], 'string', 'max' => 255],
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
            'date_notice' => 'Date Notice',
            'truck_number' => 'Truck Number',
            'truck_number_reg' => 'Truck Number Reg',
            'invoice_number' => 'Invoice Number',
            'provider_id' => 'Provider ID',
            'article' => 'Article',
            'description' => 'Description',
            'date' => 'Date',
            'sort' => 'Sort',
            'status' => 'Status',
        ];
    }

    public function saveObject() {
        $post = Yii::$app->request->post();

        $this->user_id = Yii::$app->user->identity->id;
        $this->status = 1;

        if ($this->save()) {
            $keys = array('notice_waybill_id', 'product_id', 'amount', 'description', 'status');
            $vals = array();

            foreach ($this->products['product'] as $k => $product) {
                if ($this->products['amount'][$k]) {
                    $vals[] = [
                        'notice_waybill_id' => $this->id,
                        'product_id' => $this->products['product'][$k],
                        'amount' => $this->products['amount'][$k],
                        'description' => $this->products['description'][$k],
                        'status' => 1
                    ];
                }
            }
            
            Yii::$app->db->createCommand()->batchInsert('notice_waybill_product', $keys, $vals)->execute();

            $truck = new NoticeTruck;
            $truck->status = 0;
            $truck->notice_waybill_id = $this->id;

            if ($truck->save(false)) {
                $keys = array('notice_truck_id', 'product_id', 'amount', 'description', 'status');
                $vals = array();
                foreach ($this->products['product'] as $k => $product) {
                    if ($this->products['amount'][$k]) {
                        $vals[] = [
                            'notice_truck_id' => $truck->id,
                            'product_id' => $this->products['product'][$k],
                            'amount' => $this->products['amount'][$k],
                            'description' => $this->products['description'][$k],
                            'status' => 1
                        ];
                    }
                }

                Yii::$app->db->createCommand()->batchInsert('notice_truck_product', $keys, $vals)->execute();
            }

            return true;
        }

        return false;
    }

    /**
     * Gets query for [[NoticeTrucks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticeTrucks()
    {
        return $this->hasMany(NoticeTruck::className(), ['notice_waybill_id' => 'id']);
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
     * Gets query for [[NoticeWaybillProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticeWaybillProducts()
    {
        return $this->hasMany(NoticeWaybillProduct::className(), ['notice_waybill_id' => 'id']);
    }

    public function getProvider()
    {
        return $this->hasOne(Category::className(), ['id' => 'provider_id']);
    }
}
