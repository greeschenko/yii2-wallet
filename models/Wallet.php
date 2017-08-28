<?php

namespace greeschenko\wallet\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%wallet}}".
 *
 * @property int $id
 * @property int $created_at Створено
 * @property int $updated_at Оновлено
 * @property int $to_user Одержувач
 * @property int $from_user Відправник
 * @property int $sum Сума
 * @property int $deposit Залишок на рахунку
 * @property string $msg Повідомлення
 * @property int $type Тип
 * @property int $status Статус
 */
class Wallet extends \yii\db\ActiveRecord
{
    public $module;

    public function init()
    {
        parent::init();

        $this->module = Yii::$app->getModule('wallet');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wallet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function beforeSave($insert)
    {
        $this->from_user = Yii::$app->user->identity->id;
        $this->sum = $this->sum * 100;
        $deposit = self::getDeposit($this->to_user);
        $this->deposit = $deposit + $this->sum;

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'to_user', 'from_user', 'deposit', 'type', 'status'], 'integer'],
            [['sum'], 'number'],
            [['sum', 'msg'], 'required'],
            [['msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Створено',
            'updated_at' => 'Оновлено',
            'to_user' => 'Одержувач',
            'from_user' => 'Відправник',
            'presum' => 'Сума',
            'sum' => 'Сума',
            'deposit' => 'Залишок на рахунку',
            'msg' => 'Повідомлення',
            'type' => 'Тип',
            'status' => 'Статус',
        ];
    }

    /**
     * return user deposit value.
     */
    public static function getDeposit($id)
    {
        $res = 0;
        $data = static::find()
            ->select('id,to_user,deposit')
            ->where(['to_user' => $id])
            ->orderBy('created_at DESC')
            ->one();

        if ($data != null) {
            $res = $data->deposit;
        }

        return $res;
    }

    public function getFromUser()
    {
        return $this->hasOne($this->module->userclass, ['id' => 'from_user']);
    }

    public function getToUser()
    {
        return $this->hasOne($this->module->userclass, ['id' => 'to_user']);
    }
}
