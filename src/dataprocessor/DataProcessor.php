<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\services\Service;
use Yii;
use yii\base\Component;

/**
 * Class DataProcessor
 *
 *  $config = [
 *     'dataHandlerClass' => SitemapDataHandler::class,
 *     'pageSize' => 5
 * ];
 *
 * DataProcessor::exec($config);
 *
 * @package concepture\yii2logic\dataprocessor
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DataProcessor extends Component
{
    public $dataHandlerClass;
    public $queryCondition;
    public $pageSize = 50;
    public $isDone = false;
    public $totalCount;
    public $currentPage = 0;
    public $targetPage = 0;
    public $bySinglePage = false;

    /** @var \DateTime Время начала выполнения скрипта */
    protected $timeStart;

    public static function exec($config, &$inputData = null)
    {
        $processor = new static($config);
        $processor->execute($inputData);
    }

    public function init()
    {
        parent::init();
        $this->timeStart = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
    }

    /**
     * выполняет запрос и обработку данных
     *
     * @param array $inputData
     * @return type
     */
    public function execute(&$inputData = null)
    {
        if (! $inputData){
            $inputData = [];
        }

        $this->beforeExecute($inputData);
        if (! $this->isExecute($inputData))
        {
            return true;
        }

        do {
            try{
                $this->_execute($inputData);
            } catch (\Exception $dbEx){
                $this->noDbConnectionExceptionActions([], $dbEx);
                continue;
            }
        } while (! $this->isDone());

        $this->afterExecute($inputData);

        return true;
    }

    /**
     * exec query
     *
     * @param array $inputData //массив с входными данными, например для внесения полученных данных
     *
     * @return boolean
     */
    public function _execute(&$inputData = null)
    {
        $models = $this->executeQuery($inputData);
        foreach ($models as $model) {
            try{
                $this->prepareModel($model);
                $this->processModel($model, $inputData);
                $this->finishProcessModel($model, $inputData);
            } catch (\Exception $dbEx){
                $this->noDbConnectionExceptionActions($model, $dbEx);
                continue;
            }
        }

        $this->afterPageProcess($inputData);
        if ($this->bySinglePage){
            $this->isDone = true;
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isDone()
    {

        return $this->isDone;
    }

    /**
     * @return Service
     */
    protected function getService()
    {
        $dataHandlerClass = $this->dataHandlerClass;

        return $dataHandlerClass::getService();
    }


    /**
     *  get rows by sql
     */
    private function executeQuery($inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $service = $this->getService();
        $query = $service->getQuery();
        $dataHandlerClass::setupQuery($query, $inputData);
        $condition = $this->queryCondition;
        if (is_callable($condition)){
            call_user_func($condition, $query);
        }

        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        $config = [
            'pagination' => [
                'pageSize' => $this->pageSize,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'page' => $this->currentPage
            ],
            'query' => $query
        ];
        $dataProvider = $service->getDataProvider([], $config);
        $models = $dataProvider->getModels();
        if (! $this->totalCount) {
            $this->totalCount = $dataProvider->getTotalCount();
        }

        $this->currentPage = $dataProvider->getPagination()->getPage();
        if ($this->currentPage+1 == $dataProvider->getPagination()->getPageCount()){
            $this->isDone = true;
        }

        if ($dataProvider->getCount() == 0){
            $this->isDone = true;
        }

        $this->currentPage +=1;

        return $models;
    }

    /**
     * return total row count
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public function beforeExecute(&$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::beforeExecute($this, $inputData);
    }

    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public function afterExecute(&$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::afterExecute($this, $inputData);
    }

    /**
     *
     * @param type $inputData
     */
    public function isExecute(&$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        return $dataHandlerClass::isExecute($this, $inputData);
    }

    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public function finishProcessModel(&$data, &$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::finishProcessModel($this, $data, $inputData);
    }

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function afterPageProcess(&$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::afterPageProcess($this, $inputData);
    }

    /**
     * Дейсвия при недоступности БД
     *
     * @param array $data
     * @param \yii\db\Exception $exception
     */
    public function noDbConnectionExceptionActions($data, $exception)
    {
        echo " ОШИБКА!!!  ".$exception->getMessage().PHP_EOL;
    }

    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     *
     * return array
     */
    public function prepareModel(&$data)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::prepareModel($this, $data);
    }

    /**
     * Выполняем необходимые действия с данными
     */
    public function processModel(&$data, &$inputData = null)
    {
        $dataHandlerClass = $this->dataHandlerClass;
        $dataHandlerClass::processModel($this, $data, $inputData);
    }
}