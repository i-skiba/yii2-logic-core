<?php

namespace concepture\yii2logic\actions\web\v2\domain;

use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Экшен для создания сущности с доменом
 *
 * Class CreateAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    /**
     * @var string
     */
    public $view = 'create';
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'create';
    /**
     * @var string
     */
    public $scenario = ScenarioEnum::INSERT;

    /**
     * @param integer $id
     * @param integer $domain_id
     *
     * @return string HTML
     */
    public function run($domain_id = null)
    {
        $model = $this->getForm();
        $model->scenario = $this->scenario;
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        $model->domain_id = $domain_id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) !== false) {
                # todo: объеденить все условия редиректов, в переопределенной функции redirect базового контролера ядра (logic)
                if ( RequestHelper::isMagicModal()){
                    return $this->controller->responseJson([
                        'data' => $result,
                    ]);
                }
                if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                    $redirectStore = $this->getController()->redirectStoreUrl();
                    if($redirectStore) {
                        return $redirectStore;
                    }

                    # todo: криво пашет
                    return $this->redirectPrevious([$this->redirect, 'id' => $result->id, 'domain_id' => $domain_id]);
                } else {
                    return $this->redirect(['update', 'id' => $result->id, 'domain_id' => $domain_id]);
                }
            }
        }

        return $this->render($this->view, [
            'model' => $model,
            'domain_id' => $domain_id,
        ]);
    }
}