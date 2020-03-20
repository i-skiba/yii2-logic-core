<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Класс перечисления который содержит константы для статусов
 *
 * Class StatusEnum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StatusEnum extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    public static function labels()
    {
        return [
            self::ACTIVE => Yii::t('core', "Опубликован"),
            self::INACTIVE => Yii::t('core', "Черновик"),
        ];
    }
}
