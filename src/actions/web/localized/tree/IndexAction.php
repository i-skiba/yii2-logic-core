<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;

/**
 * Class IndexAction
 * @package concepture\yii2logic\actions\web\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    use LocalizedTrait;

    /**
     * @var string
     */
    public $view = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'getDataProvider';

    /**
     * @var bool
     */
    public $storeUrl = true;

    /**
     * @inheritDoc
     */
    public function run($locale = null, $parent_id = null)
    {
        $this->rememberUrl();
        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel::setLocale($locale);
        $searchModel->parent_id = $parent_id;
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);
        if($this->storeUrl) {
            $this->getController()->storeUrl();
        }

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}