<?php
namespace concepture\yii2logic\actions\web\tree;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Экшен для восстановления нефизически удаленной сущности
 *
 * Class UndeleteAction
 * @package cconcepture\yii2logic\actions\web\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UndeleteAction extends Action
{
    public $redirect = 'index';
    public $serviceMethod = 'undelete';

    public function run($id, $parent_id = null)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect, 'parent_id' => $parent_id]);
    }

    /**
     * Возвращает модель для удаления
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->findById($id);
    }
}