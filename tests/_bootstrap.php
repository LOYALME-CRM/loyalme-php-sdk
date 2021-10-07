<?php

\Codeception\Util\Autoload::addNamespace('LoyalmeCRM\LoyalmePhpSdk', 'src');

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include __DIR__ . '/../vendor/autoload.php';
}
