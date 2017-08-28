<?php

use yii\grid\GridView;
use greeschenko\wallet\models\Wallet;

/* @var $this yii\web\View */
/* @var $searchModel greeschenko\wallet\models\WalletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Особистий рахунок користувача';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-index">
    <p class="lead text-right"><?=($deposit / 100)?> UAH</p>

    <?php echo $this->render('_form', ['model' => $model]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'created_at:datetime',
            [
                'attribute' => 'from_user',
                'value' => function ($data) {
                    if ($data->fromUser != null) {
                        return $data->fromUser->genSelfOrgName();
                    }

                    return false;
                },
            ],
            [
                'attribute' => 'sum',
                'value' => function ($data) {
                    return $data->sum / 100;
                },
            ],
            'msg',
            [
                'attribute' => 'deposit',
                'value' => function ($data) {
                    return $data->deposit / 100;
                },
            ],
            // 'type',
            // 'status',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
