<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model greeschenko\wallet\models\Wallet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wallet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'to_user')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'sum')->textInput() ?>

    <?= $form->field($model, 'msg')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Надіслати', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
