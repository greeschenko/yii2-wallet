<?php

namespace greeschenko\wallet;

class Module extends \yii\base\Module
{
    const VER = '0.1-dev';

    public $userclass;

    public function init()
    {
        parent::init();

        $this->components = [];
    }
}
