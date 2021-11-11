<?php

namespace app\models\gp;

use app\models\user\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "b_department_plastic".
 *
 * @property int $id
 * @property int $model_id
 * @property int|null $user_id
 * @property int|null $part_model
 * @property string|null $current_operation
 * @property string $number_poddon
 * @property int|null $is_defect
 * @property int $amount
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 * @property ProductModel $model
 */
class DepartmentPlastic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'b_department_plastic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'number_poddon', 'amount', 'status'], 'required'],
            [['model_id', 'user_id', 'part_model', 'is_defect', 'department_id', 'amount', 'status', 'created_at', 'updated_at'], 'integer'],
            [['current_operation', 'number_poddon'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductModel::className(), 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
//                 'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function removeObject(){
        return $this->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'Модель',
            'user_id' => 'Пользователь',
            'department_id' => 'Отдел',
            'part_model' => 'Часть',
            'current_operation' => 'Текущая операция',
            'number_poddon' => 'Номер поддона',
            'is_defect' => 'Дефект',
            'amount' => 'Кол-во',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
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
     * Gets query for [[Model]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(ProductModel::className(), ['id' => 'model_id']);
    }
    public function getDepartment()
    {
        return $this->hasOne(BDepartment::className(), ['id' => 'department_id']);
    }


    public function getDeffect()
    {
        return $this->hasMany(AllDeffect::className(), ['dep_id' => 'id']);
    }
    public function getBuffer()
    {
        return $this->hasMany(BufferZone::className(), ['dep_id' => 'id']);
    }

}
