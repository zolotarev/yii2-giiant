# Providers

Example configuration 

```
<?php
namespace temp;

use zolotarev\giiant\crud\callbacks\base\Callback;
use zolotarev\giiant\crud\callbacks\yii\Db;
use zolotarev\giiant\crud\callbacks\yii\Html;

\Yii::$container->set(
    'zolotarev\giiant\crud\providers\CallbackProvider',
    [
        'columnFormats'    => [
            // hide system fields, but not ID in table
            'created_at$|updated_at$' => Callback::false(),
            // hide all TEXT or TINYTEXT columns
            '.*'                      => Db::falseIfText(),
        ],
        'activeFields'     => [
            // hide system fields in form 
            'id$|created_at$|updated_at$' => Callback::false(),
        ],
        'attributeFormats' => [
            // render HTML output
            '_html$' => Html::attribute(),
        ],

    ]
);
```