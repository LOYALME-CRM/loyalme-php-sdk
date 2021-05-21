<?php

namespace LoyalmeCRM\LoyalmePhpSdk\Interfaces;

interface ConnectionInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public function sendGetRequest(string $action, array $params);
    public function sendPostRequest(string $action, array $params);
    public function sendPutRequest(string $action, array $params);
    public function sendDeleteRequest(string $action, array $params);
}
