<?php

namespace concepture\yii2logic\pojo;

/**
 * Модель для данных по соцсетям
 *
 * Class Social
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Social extends Pojo
{
    public $social;
    public $url;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'social',
                    'url',
                ],
                'required'
            ],
            [
                [
                    'url',
                ],
                'string'
            ],
            [
                [
                    'social',
                ],
                'string'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'social' => \Yii::t('core','Социальная сеть'),
            'url' => \Yii::t('core','Адрес'),
        ];
    }
}