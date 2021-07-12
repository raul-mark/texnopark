<?php
namespace app\models\user;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

use app\models\Images;
use app\models\moderator\ModeratorAccess;
use app\models\Category;
use app\models\feedback\Feedback;

use yii\services\Sms;

class User extends ActiveRecord implements IdentityInterface {
    // roles
    const ROLE_ADMIN = 1;
    const ROLE_MODERATOR = 2;
    const ROLE_USER = 3;
    const ROLE_STOCK = 4;
    const ROLE_WORKER = 5;
    const ROLE_WORKER_SHOP = 6;

    // photo settings
    const PHOTO_PATH = 'uploads/user/';
    const PHOTO_DEFAULT = '/assets_files/img/user.png';

    // extra variales
    public $user_gallery = [];
    public $imageFiles = [];
    public $moderator_access = [];
    public $remember;
    public $password_repeat;

    // scenarios
    // admin
    const SIGNIN_ADMIN = 'signin_admin';
    const SIGNUP_ADMIN_USER = 'singup_admin_user';
    const UPDATE_ADMIN_USER = 'update_admin_user';

    // moderator
    const SIGNUP_MODERATOR = 'signup_moderator';
    const UPDATE_MODERATOR = 'update_moderator';

    // worker
    const SIGNUP_WORKER = 'signup_worker';
    const UPDATE_WORKER = 'update_worker';

    // shop
    const SIGNUP_WORKER_SHOP = 'signup_worker_shop';
    const UPDATE_WORKER_SHOP = 'update_worker_shop';

    // user
    const SIGNIN_USER = 'signin_user';
    const SIGNUP_USER = 'signup_user';
    const UPDATE_USER = 'update_user';
    const FORGOT_PASSWORD = 'forgot_password';
    const RECOVER_PASSWORD = 'recover_password';

    public static function tableName() {
        return 'user';
    }

    public function rules() {
        return [
            // moderator
            // sign-up
            [['name', 'login', 'password'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNUP_MODERATOR],
            ['login', 'checkLogin', 'on'=>self::SIGNUP_MODERATOR],

            // update
            [['name', 'login'], 'required', 'message'=>'Заполните поле', 'on'=>self::UPDATE_MODERATOR],
            ['login', 'checkLogin', 'on'=>self::UPDATE_MODERATOR],

            // worker
            // sign-up
            [['name', 'login', 'password', 'type'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNUP_WORKER],
            ['login', 'checkLogin', 'on'=>self::SIGNUP_WORKER],

            // update
            [['name', 'login', 'type'], 'required', 'message'=>'Заполните поле', 'on'=>self::UPDATE_WORKER],
            ['login', 'checkLogin', 'on'=>self::UPDATE_WORKER],

            // worker shop
            // sign-up
            [['name', 'login', 'password'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNUP_WORKER_SHOP],
            ['login', 'checkLogin', 'on'=>self::SIGNUP_WORKER_SHOP],

            // update
            [['name', 'login'], 'required', 'message'=>'Заполните поле', 'on'=>self::UPDATE_WORKER_SHOP],
            ['login', 'checkLogin', 'on'=>self::UPDATE_WORKER_SHOP],

            // admin
            // sing in to admin panel
            [['login', 'password'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNIN_ADMIN],
            ['password', 'checkPassword', 'on'=>self::SIGNIN_ADMIN],

            // save user by admin
            [['password', 'name', 'phone'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNUP_ADMIN_USER],
            [['name', 'phone'], 'required', 'message'=>'Заполните поле', 'on'=>self::UPDATE_ADMIN_USER],
            ['phone', 'checkPhone', 'on'=>self::SIGNUP_ADMIN_USER],
            ['email', 'checkEmail', 'on'=>self::SIGNUP_ADMIN_USER],
            ['phone', 'checkPhoneAdmin', 'on'=>self::UPDATE_ADMIN_USER],

            // user
            // sign in
            [['email', 'password'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNIN_USER],
            ['password', 'checkPassword', 'on'=>self::SIGNIN_USER],

            // sign up user
            [['email', 'password'], 'required', 'message'=>'Заполните поле', 'on'=>self::SIGNUP_USER],
            // [['phone'], 'required', 'message'=>'Заполните поле', 'on'=>self::UPDATE_USER],
            ['email', 'checkEmail', 'on'=>self::SIGNUP_USER],
            ['email', 'checkEmail', 'on'=>self::UPDATE_USER],
            // ['phone', 'checkPhone', 'on'=>self::SIGNUP_USER],
            // ['phone', 'checkPhone', 'on'=>self::UPDATE_USER],

            // recover password
            [['phone'], 'required', 'message'=>'Заполните поле', 'on'=>self::FORGOT_PASSWORD],
            ['phone', 'checkPhoneExists', 'on'=>self::FORGOT_PASSWORD],
            [['password', 'password_repeat'], 'required', 'message'=>'Заполните поле', 'on'=>self::RECOVER_PASSWORD],
            ['password', 'compare', 'compareAttribute'=>'password_repeat', 'message'=>"Пароли не совпадают", 'on'=>self::RECOVER_PASSWORD],

            // default validation
            // [['email'], 'email', 'message'=>'Не верный формат e-mail'],
            //[['email'], 'unique'],
            [['password', 'password_repeat'], 'string', 'min'=>6, 'message'=>'Пароль не может быть менее 6 символов'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Пароли не совпадают"],
            [['login', 'phone', 'email', 'name', 'lastname', 'fname', 'ip', 'token', 'birthday', 'type', 'address_fact', 'inn', 'mfo'], 'string'],
            [['role', 'status', 'region_id', 'gender', 'admin_style'], 'integer'],
            [['date', 'moderator_access', 'remember', 'device_token'], 'safe'],
            [['user_gallery'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 2048000],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 2048000]
        ];
    }

    public static function findIdentity($id){
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        return static::findOne(['token'=>$token]);
    }

    public static function findByUsername($username){
        $user = static::findOne(['login'=>$username]);
        
        if (!$user) {
            $user = static::findOne(['phone'=>$username]);
        }

        if (!$user) {
            $user = static::findOne(['email'=>$username]);
        }

        if (!$user) {
            $user = static::findOne(['login'=>$username]);
        }

        return $user;
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthKey(){
        return $this->authKey;
    }

    public function validateAuthKey($authKey){
        return $this->authKey === $authKey;
    }

    //helpers
    public function setPassword($password){
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function generatePassword($password) {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function generatePasswordResetToken(){
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();    
    }

    public function generateAuthKey(){
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function validatePassword($password){
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function checkPhoneExists($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!preg_match("/^[\d]+$/", $this->phone)) {
                return $this->addError($attribute, 'Вводите только цифры');
            }
            if ((mb_strlen($this->phone) < 12) || (mb_strlen($this->phone) > 12)) {
                return $this->addError($attribute, 'Количество цифр должно быть 12');
            }

            $user = $this->findByUsername($this->phone);

            if (!$user) {
                return $this->addError($attribute, 'Такого номера в базе нет');
            }
        }

        return false;
    }

    // validate check password
    public function checkPassword($attribute, $params) {
        //return true;
        if (!$this->hasErrors()) {
            if ($this->login) {
                $user = $this->findByUsername($this->login);
            }
            if ($this->phone) {
                $user = $this->findByUsername($this->phone);
            }
            if ($this->email) {
                $user = $this->findByUsername($this->email);
            }

            $error_login = 'Не верный логин и/или пароль';
            if (!$user || !$user->validatePassword($this->password)) {
                return $this->addError($attribute, $error_login);
            }
        }

        return false;
    }

    // check exist login
    public function checkLogin($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->findByUsername($this->login);
            if ($user && ($user->id != $this->id)) {
                return $this->addError($attribute, 'Логин уже занят');
            }
        }

        return false;
    }

    // check exist email
    public function checkEmail($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->email) {
                $user = $this->findByUsername($this->email);
                // echo $user->id.':'.$this->id;
                // die;
                if ($user && ($user->id != $this->id)) {
                    return $this->addError($attribute, 'E-mail уже занят');
                }
            }
        }

        return false;
    }

    // check exist phone
    public function checkPhone($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!preg_match("/^[\d]+$/", $this->phone)) {
                return $this->addError($attribute, 'Вводите только цифры');
            }
            if ((mb_strlen($this->phone) < 12) || (mb_strlen($this->phone) > 12)) {
                return $this->addError($attribute, 'Количество цифр должно быть 12');
            }

            $user = $this->findByUsername($this->phone);

            if ($user && ($user->id != $this->id)) {
                return $this->addError($attribute, 'Номер телефона уже занят');
            }
        }

        return false;
    }

    // check exist phone
    public function checkPhoneAdmin($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!preg_match("/^[\d]+$/", $this->phone)) {
                return $this->addError($attribute, 'Вводите только цифры');
            }
            if ((mb_strlen($this->phone) < 12) || (mb_strlen($this->phone) > 12)) {
                return $this->addError($attribute, 'Количество цифр должно быть 12');
            }

            $user = $this->findByUsername($this->phone);

            if ($user && ($user->id != $this->id) && ($user->status == 1)) {
                return $this->addError($attribute, 'Номер телефона уже занят');
            }
        }

        return false;
    }

    // check exist phone
    public function checkPhoneAuth($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!preg_match("/^[\d]+$/", $this->phone)) {
                return $this->addError($attribute, 'Вводите только цифры');
            }
            // if ((mb_strlen($this->phone) < 12) || (mb_strlen($this->phone) > 12)) {
            //     return $this->addError($attribute, 'Количество цифр должно быть 12');
            // }

            $user = $this->findByUsername($this->phone);

            if ($user && ($user->id != $this->id) && ($user->status == 1)) {
                return $this->addError($attribute, 'Номер телефона уже занят');
            }
        }

        return false;
    }

    // save user
    public function saveUser($type = self::ROLE_USER, $status = 0) {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->role = $type;

        $this->status = $status;

        $current_image = $this->image ? $this->image : null;

        if (!$this->gender) {
            $this->gender = 1;
        }

        if ($this->save()) {
            if ($this->moderator_access) {
                ModeratorAccess::deleteAll('user_id = :user_id', ['user_id'=>$this->id]);

                $keys = array('user_id', 'moderator_id');
                $vals = array();
                foreach ($this->moderator_access as $key => $access) {
                    $vals[$key]['user_id'] = $this->id;
                    $vals[$key]['moderator_id'] = $access;
                }
                Yii::$app->db->createCommand()->batchInsert('moderator_access', $keys, $vals)->execute();
            }
            
            // upload file
            $image = new Images;
            // image
            if ($image->imageFiles = UploadedFile::getInstances($this, 'imageFiles')) {
                if ($current_image) {
                    $current_image->removeImageSize();
                }
                $image->uploadPhoto($this->id, 'user');
            }

            // gallery
            $gallery = new Images;
            if ($gallery->imageFiles = UploadedFile::getInstances($this, 'user_gallery')) {
                $gallery->uploadPhoto($this->id, 'user', 2);
            }

            return $this;
        }

        return false;
    }

    public function saveCode($phone = null) {
        $code = new SmsCode;
        
        $current_phone = $phone ? $phone : $this->phone;
        $model = SmsCode::find()->where(['phone'=>$current_phone]);

        if ($this->id) {
            $model->andWhere(['user_id'=>$this->id]);
            $code->user_id = $this->id;
        }

        $model = $model->one();

        if ($model) {
            $model->delete();
        }
        
        $code->phone = $current_phone;
        // $code->code = (string)$this->generateCode();
        $code->code = '000000';
        $code->sms_expire = strtotime('+3 minute');

        if ($code->save()) {
            $service = new Sms;
            $service->send($code->phone, "Tutortop.me\nВаш код: ".$code->code);
            return $code;
        }

        return false;
    }

    public function generateFileName() {
        return time()+mt_rand(0, 1000000);
    }

    public function generateCode() {
        return mt_rand(100000, 999999);
    }

    public function generateToken() {
        return Yii::$app->security->generateRandomString();
    }

    public function removeUser(){
        if ($this->image) {
            $this->image->removeImageSize();
        }

        return $this->delete();
    }

    public function login() {
        if ($this->login) {
            $user = $this->findByUsername($this->login);
        }

        if ($this->phone) {
            $user = $this->findByUsername($this->phone);
        }

        if ($this->email) {
            $user = $this->findByUsername($this->email);
        }

        if ($user) {
            if (Yii::$app->user->login($user, $this->remember ? 3600*24*30 : 0)) {
                return $user;
            }
        }
        return false;
    }

    public function changePassword() {
        $this->password ? $this->setPassword(Html::encode($this->password)) : $this->password = $this->getOldAttributes()['password'];
        return $this->save(false) ? true : false;
    }

    public function getPhoto($s = 'original') {
        if ($this->image) {
            $path = self::PHOTO_PATH.$this->image->object_id.'/'.$s.'/'.$this->image->photo;

            if (is_file($path)) {
                return '/'.$path;
            }
        }

        return self::PHOTO_DEFAULT;
    }

    public function getGallery($s = 'original') {
        $data = [];

        if ($this->photos) {
            foreach ($this->photos as $photo) {
                $data[] = [
                    'photo' => '/'.self::PHOTO_PATH.$photo->object_id.'/'.$s.'/'.$photo->photo,
                    // 'verified' => $photo->status
                ];
            }
        }

        return $data;
    }

    // relations
    public function getImage() {
        return $this->hasOne(Images::className(), ['object_id'=>'id'])->andOnCondition(['type'=>'user', 'main'=>1]);
    }

    public function getPhotos() {
        return $this->hasMany(Images::className(), ['object_id'=>'id'])->andOnCondition(['type'=>'user', 'main'=>2]);
    }

    public function getModeratorAccess() {
        return $this->hasMany(ModeratorAccess::className(), ['user_id'=>'id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Category::className(), ['id' => 'region_id']);
    }
}