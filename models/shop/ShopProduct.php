<?php

namespace app\models\shop;

use Yii;
use app\models\shop\Shop;
use app\models\product\Product;
use app\models\shipment\Shipment;

/**
 * This is the model class for table "shop_product".
 *
 * @property int $id
 * @property int|null $shop_id
 * @property int|null $product_id
 * @property float|null $amount
 * @property int $status
 * @property int $sort
 * @property string $date
 *
 * @property Product $product
 * @property Shop $shop
 */
class ShopProduct extends \yii\db\ActiveRecord
{
    public $products = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'product_id', 'shop_stack_id', 'shop_stack_shelving_id', 'status', 'sort'], 'integer'],
            [['amount'], 'number'],
            [['date', 'products'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
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
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'status' => 'Status',
            'sort' => 'Sort',
            'date' => 'Date',
        ];
    }

    public function saveObject() {
        $shipment = Shipment::findOne(Yii::$app->request->get('id'));
        if ($shipment) {
            $shipment->status = 1;
            $shipment->save(false);
        }
        $keys = ['shop_id', 'product_id', 'shop_stack_id', 'shop_stack_shelving_id', 'amount', 'status'];
        $vals = [];

        foreach ($this->products['product'] as $k => $product) {
            if ($this->products['amount'][$k]) {
                $vals[] = [
                    'shop_id' => 1,
                    'product_id' => $this->products['product'][$k],
                    'shop_stack_id' => $this->products['stack_id'][$k],
                    'shop_stack_shelving_id' => $this->products['shelf_id'][$k],
                    'amount' => $this->products['amount'][$k],
                    'status' => 1,
                ];

                $product = Product::findOne($this->products['product'][$k]);

                if (!$product) {
                    $product = new Product;
                }

                $product->amount = $product->amount-$this->products['amount'][$k];
                $product->stock_id = $this->products['stock_id'][$k];
                $product->stack_id = $this->products['stack_id'][$k];
                $product->shelf_id = $this->products['shelf_id'][$k];
                $product->save(false);
            }
        }
        
        Yii::$app->db->createCommand()->batchInsert('shop_product', $keys, $vals)->execute();

        return true;
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
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

    public function getShopStack()
    {
        return $this->hasOne(ShopStack::className(), ['id' => 'shop_stack_id']);
    }

    public function getShopStackShelving()
    {
        return $this->hasOne(ShopStackShelving::className(), ['id' => 'shop_stack_shelving_id']);
    }
}
