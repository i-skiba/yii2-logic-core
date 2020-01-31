<?php

namespace concepture\yii2logic\services\interfaces;

/**
 * Interface DynamicElementsEventInterface
 * @package concepture\yii2logic\services\interfaces
 */
interface ModifyEventInterface
{
    const EVENT_BEFORE_MODEL_SAVE = 'beforeModelSave';
    const EVENT_AFTER_MODEL_SAVE = 'afterModelSave';
    const EVENT_BEFORE_CREATE = 'beforeCreate';
    const EVENT_AFTER_CREATE = 'afterCreate';
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    const EVENT_AFTER_UPDATE = 'afterUpdate';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
}