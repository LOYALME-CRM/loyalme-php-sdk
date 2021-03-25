<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

class Connection implements ConnectionInterface
{
    private $_url;
    private $_path = '';
    private $_token;
    private $_brandId;
    private $_pointId;
    private $_personId;

    public function __construct(
        string $token,
        string $url,
        int $brandId,
        int $pointId,
        int $personId
    ) {
        $this->_token = $token;
        $this->_url = $url;
        $this->_brandId = $brandId;
        $this->_pointId = $pointId;
        $this->_personId = $personId;
    }

    private function _sendRequest(string $method, array $params)
    {
        $url = $this->_url . '/' . $this->getPath();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [sprintf('Authorization: Bearer %s', $this->_token), 'accept: application/json', 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (in_array($method, [self::METHOD_POST, self::METHOD_PUT])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);

        return $output;
    }

    protected function _setPath(string $path)
    {
        $this->_path = $path;
        return $this;
    }

    public function sendGetRequest(string $action, array $params)
    {
        $this->_setPath($action . '?' . http_build_query($params, '', '&'));

        return $this->_sendRequest(self::METHOD_GET, $params);
    }

    public function sendPostRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_POST, $params);
    }

    public function sendPutRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_PUT, $params);
    }

    public function sendDeleteRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_PUT, $params);
    }

    public function getPath(): string
    {
        return $this->_path;
    }
}
