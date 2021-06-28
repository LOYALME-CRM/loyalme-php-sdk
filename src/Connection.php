<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ConnectionInterface;

class Connection implements ConnectionInterface
{
    const STATUS_CODE_SUCCESS = 200;
    const STATUS_CODE_NOT_FOUND = 404;

    private $_url;
    private $_path = '';
    private $_token;
    public $brandId;
    public $pointId;
    public $personId;

    /**
     * @param string $token
     * @param string $url
     * @param int $brandId
     * @param int $pointId
     * @param int $personId
     */
    public function __construct(
        string $token,
        string $url,
        int $brandId,
        int $pointId,
        int $personId
    ) {
        $this->_token = $token;
        $this->_url = $url;
        $this->brandId = $brandId;
        $this->pointId = $pointId;
        $this->personId = $personId;
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     */
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
        $output = json_decode($output, true);
        return $output;
    }

    /**
     * @param string $path
     * @return $this
     */
    protected function _setPath(string $path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function sendGetRequest(string $action, array $params)
    {
        if ($params) {
            $this->_setPath($action . '?' . http_build_query($params, '', '&'));
        } else {
            $this->_setPath($action);
        }

        return $this->_sendRequest(self::METHOD_GET, $params);
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function sendPostRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_POST, $params);
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function sendPutRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_PUT, $params);
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function sendDeleteRequest(string $action, array $params)
    {
        $this->_setPath($action);

        return $this->_sendRequest(self::METHOD_DELETE, $params);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }
}
