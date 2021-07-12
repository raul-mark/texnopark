<?php

namespace app\models\shop;

use Yii;

/**
 * This is the model class for table "shop_stack".
 *
 * @property int $id
 * @property int|null $shop_id
 * @property string|null $stack_number
 * @property string|null $shelfs_count
 * @property int $status
 * @property int $sort
 * @property string $date
 *
 * @property Shop $shop
 * @property ShopStackShelving[] $shopStackShelvings
 */
class ShopStack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_stack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'status', 'sort'], 'integer'],
            [['date'], 'safe'],
            [['stack_number', 'shelfs_count'], 'string', 'max' => 255],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'stack_number' => 'Stack Number',
            'shelfs_count' => 'Shelfs Count',
            'status' => 'Status',
            'sort' => 'Sort',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[Shop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }

    /**
     * Gets query for [[ShopStackShelvings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShopStackShelvings()
    {
        return $this->hasMany(ShopStackShelving::className(), ['shop_stack_id' => 'id']);
    }
}
