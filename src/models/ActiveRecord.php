<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\traits\ModelSupportTrait;
use Yii;
use concepture\yii2logic\actions\traits\ModelScenarioTrait;
use concepture\yii2logic\models\traits\NonPhysicalDeleteTrait;
use concepture\yii2logic\models\traits\SearchTrait;
use Exception;
use yii\db\ActiveRecord as Base;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use concepture\yii2logic\db\ActiveQuery;

/**
 * Базовая модель для сущности
 *
 * Class ActiveRecord
 * @package cconcepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class ActiveRecord extends Base
{
    use ModelScenarioTrait;
    use NonPhysicalDeleteTrait;
    use SearchTrait;
    use ModelSupportTrait;

    /**
     * Для случаев когда значение активного статуса отличается от стандартного
     * @return integer
     */
    public function getActiveStatusValue()
    {
        return StatusEnum::ACTIVE;
    }

    /**
     * Возвращает сервис
     * @return Service
     */
    public static function getService()
    {
        $name = ClassHelper::getServiceName(static::class, ['Search']);

        return Yii::$app->{$name};
    }

    /**
     * Првоерка является ли атрибут полем в бд
     *
     * @param $attribute
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function isDbField($attribute)
    {
        $column = $this->getTableSchema()->getColumn($attribute);
        if ($column) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает тип данных атрибута из базы
     * @param $attribute
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function getAttrDbType($attribute)
    {
        $column = $this->getTableSchema()->getColumn($attribute);
        if ($column) {
            return $column->dbType;
        }

        throw new \yii\db\Exception("table not have field " . $attribute);
    }

    public static function find()
    {
        return Yii::createObject(ActiveQuery::class, [get_called_class()]);
    }

    /**
     * Врубаем транзакции по уолчнию для всех случаев модификации данных для сценария default
     * Для использования в стандартном методе AR   ::isTransactional($operation)
     *
     * @return array
     */
    public function transactions()
    {
        return [
            'default' => self::OP_ALL
        ];
    }

    /**
     * Добавляет в Дата провайдер сортировку по атрибуту связанной таблицы
     *
     * @param ActiveDataProvider $dataProvider
     * @param $attribute
     */
    protected function addSortByRelatedAttribute(ActiveDataProvider $dataProvider, $tableAlias, $attribute)
    {
        $dataProvider->sort->attributes[$attribute] = [
            'asc' => ["{$tableAlias}.{$attribute}" => SORT_ASC],
            'desc' => ["{$tableAlias}.{$attribute}" => SORT_DESC],
        ];
    }

    /**
     * Метод для расширения ActiveQuery
     * определяется в Search модели
     *
     * применяетсяв методе getDataProvider в concepture\yii2logic\services\traits\ReadTrait.php
     *
     *
     * Пример
     *   public function extendQuery(ActiveQuery $query)
     *   {
     *         $query->andFilterWhere([
     *            'id' => $this->id
     *         ]);
     *
     *         $query->andFilterWhere(['like', 'username', $this->username]);
     *   }
     *
     *
     * @param ActiveQuery $query
     */
    public function extendQuery(\yii\db\ActiveQuery $query){}

    /**
     * Метод для расширения DataProvider
     * используетсяв Search модели
     *
     * Пример
     *   public function extendDataProvider(ActiveDataProvider $dataProvider)
     *   {
     *       $dataProvider->sort->attributes['username'] = [
     *           'asc' => [User::tableName().'.username' => SORT_ASC],
     *           'desc' => [User::tableName().'.username' => SORT_DESC],
     *       ];
     *       $dataProvider->sort->attributes['caption'] = [
     *           'asc' => [UserRoleHandbook::tableName().'.caption' => SORT_ASC],
     *           'desc' => [UserRoleHandbook::tableName().'.caption' => SORT_DESC],
     *       ];
     *   }
     *
     * @param ActiveDataProvider $dataProvider
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider) {
        if( ! $dataProvider->sort->defaultOrder && $this->hasAttribute('id')) {
            $tableName = trim(static::tableName(), '{}%');
            $dataProvider->sort->defaultOrder = ['id' => 'desc'];
            $dataProvider->sort->attributes['id'] = [
                'asc' => [$tableName . '.id' => SORT_ASC],
                'desc' => [$tableName . '.id' => SORT_DESC],
            ];
        }
    }

    /**
     * Аттрибут модели который будет использован для ключа в выпадающих списках
     * используетсяв Search модели
     *
     *
     * Пример
     *       public static function getListSearchKeyAttribute()
     *       {
     *           return 'id';
     *       }
     *
     *
     * @return string
     */
    public static function getListSearchKeyAttribute()
    {
        return null;
    }

    /**
     * Список атрибутов для выборки \yii\jui\AutoComplete
     *
     * @return array
     */
    public static function getListSearchAttributes()
    {
        return [];
    }

    /**
     * Аттрибут модели который будет использован для метки в выпадающих списках
     * используетсяв Search модели
     *
     *   public static function getListSearchAttribute()
     *   {
     *       return 'username';
     *   }
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   при работе методов сервисного треита CatalogTrait
     *   будет вызван метод getLabel() модели
     *
     * @return string
     */
    public static function getListSearchAttribute()
    {
        return null;
    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     */
    public static function label()
    {
        return static::class;
    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     */
    public function toString()
    {

        return null;
    }


    /**
     * Возвращает массив с атрибутами которые быди изменены
     * @return array
     */
    public function getChangedAtributes()
    {
        $result = [];
        foreach ($this->oldAttributes as $attribute => $value) {
            if ($this->{$attribute} == $value) {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }

    /**
     * Проверка изменились ли какие либо атрибуты модели
     *
     * @return bool
     */
    public function isAnyAttributeChanged()
    {
        $changed = $this->getChangedAtributes();

        return ! empty($changed);
    }

    /**
     * Устанавливает поле updated_at (Должно быть datetime)
     *
     *
     */
    public function setUpdatedAt()
    {
        if ($this->hasAttribute("updated_at")) {
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }
}