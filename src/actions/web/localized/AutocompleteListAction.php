<?php
namespace concepture\yii2logic\actions\web\localized;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

/**
 * @deprecated
 *
 * Экшен для работы с выпадающими списками виджет \yii\jui\AutoComplete для сущностей с локализацией
 *
 * Пример:
 *
 * search модель
 *
 *       class UserSearch extends User
 *       {
 *
 *           public static function getListSearchKeyAttribute()
 *           {
 *               return 'id';
 *           }
 *
 *           public static function getListSearchAttribute()
 *           {
 *               return 'username';
 *           }
 *       }
 *
 *
 * Контроллер:
 *
 *        public function actions()
 *       {
 *           $actions = parent::actions();
 *           $actions['list'] = AutocompleteListAction::class;
 *
 *           return $actions;
 *       }
 *
 * вьюшка
 *
 *       <?= \yii\jui\AutoComplete::widget([
 *           'options' => ['class' => 'form-control'],
 *           'clientOptions' => [
 *               'source' => \yii\helpers\Url::to(['/user/user/list']),
 *               'minLength'=>'2',
 *               'autoFill'=>true,
 *               'select' => new \yii\web\JsExpression("function( event, ui ) {
 *                    $('#userroleform-user_id').val(ui.item.id);
 *               }")]
 *       ]); ?>
 *
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AutocompleteListAction extends Action
{

    public function run($term = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(!$term){
            return [];
        }

        $data =  $this->getService()->getLocalizedAutocompleteList($term);

        return $data;
    }
}