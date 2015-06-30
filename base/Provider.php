<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:02
 */

namespace zolotarev\giiant\base;


use yii\base\Object;

class Provider extends Object
{
    /**
     * @var \zolotarev\giiant\crud\Generator
     */
    public $generator;

    public $columnNames = [];

    public $columnPatterns = [];
} 