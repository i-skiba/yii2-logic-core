<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasPropertyInterface;
use concepture\yii2logic\models\interfaces\IAmDictionaryInterface;

/**
 * ActiveRecord для моделей которые являются справочниками учитывающими домен
 *
 * Class DomainBasedDictionaryActiveRecord
 * @package concepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class DomainBasedDictionaryActiveRecord extends DomainByLocalesPropertyActiveRecord implements IAmDictionaryInterface
{

}