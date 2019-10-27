<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;

/**
 * Class IndexAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    public $view = 'index';
    public $serviceMethod = 'getDataProvider';

    public function run()
    {
        $searchClass = $this->getSearchClass();
        $searchModel = new $searchClass();
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getService()->{$this->serviceMethod}(Yii::$app->request->queryParams);

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}