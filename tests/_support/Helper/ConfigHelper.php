<?php

namespace Helper;

use Codeception\Module;

class ConfigHelper extends Module
{
    public function getConfig($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            return null;
        }
    }
}
