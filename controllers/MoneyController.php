<?php

namespace greeschenko\wallet\controllers;

use Yii;
use greeschenko\wallet\models\Wallet;
use greeschenko\wallet\models\WalletSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use greeschenko\wallet\helpers\privat\PrivatWorker;
use greeschenko\wallet\helpers\privat\PrivatHelper;
use greeschenko\wallet\helpers\privat\PrivatTest;

/**
 * DefaultController implements the CRUD actions for Wallet model.
 */
class MoneyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['privat-bill'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Wallet models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WalletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Wallet model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * display user wallet log and add form.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionForUser($id)
    {
        $model = new wallet();
        $searchModel = new WalletSearch();
        $searchModel->to_user = $model->to_user = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Операція успішна!');

            return $this->redirect(['for-user', 'id' => $id]);
        }

        $deposit = Wallet::getDeposit($id);

        return $this->render('for_user', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'deposit' => $deposit,
        ]);
    }

    /**
     * Creates a new Wallet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Wallet();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Wallet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Wallet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Wallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Wallet the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Wallet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * privat bank billing access point.
     */
    public function actionPrivatBill()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        //

        $data = PrivatWorker::process();
        $action = $data->getAttribute('action');

        switch ($action) {
            case 'Search':
                $bill_identifier = $data->find('Unit', 'bill_identifier')->getAttribute('value');
                $userdata = [
                    ['name' => 'Message', 'value' => 'message text'],
                    ['name' => 'PayerInfo', 'attributes' => ['billIdentifier' => $bill_identifier], 'value' => [
                        ['name' => 'Fio', 'value' => 'Иванов Иван Иванович'],
                        ['name' => 'Phone', 'value' => '+321234214'],
                    ]],
                    ['name' => 'ServiceGroup', 'value' => [
                        ['name' => 'DebtService', 'attributes' => ['metersGlobalTarif' => 14.65, 'serviceCode' => 101], 'value' => [
                            ['name' => 'Message', 'value' => 'Тарифы на воду были изменены, за детальной информацией обращайтесь в ГорВодоканал!'],
                        ]],
                        ['name' => 'DebtService', 'attributes' => ['serviceCode' => 102], 'value' => [
                            ['name' => 'ServiceName', 'value' => 'Квартирная плата'],
                        ]],
                    ]],
                ];
                $responce = PrivatHelper::data2xml($action, 'DebtPack', PrivatHelper::array2data($userdata));
                break;
            default:
                $responce = self::createErrorXml('Undefined action: "'.self::$action.'".', 400);
                break;
        }

        return $responce;
    }

    /**
     * test privat bank billing access point.
     */
    public function actionTestPrivatBill($id)
    {
        $url = 'http://prozorrodev.ga/wallet/money/privat-bill';
        $data = array(
            [
                'name' => 'Unit',
                'value' => '',
                'attributes' => [
                    'name' => 'bill_identifier',
                    'value' => $id,
                ],
            ],
        );
        PrivatTest::testSearch($url, $data);
    }
}
