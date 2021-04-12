<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ClientException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ActivityInterface;
use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;

class Activity extends Api implements ActivityInterface
{
    const ACTION_LIST = 'activity/list';
    const ACTION_FIRE_EVENT = 'activity/fire-event';
    /**
     * @var
     */
    public $client_id;
    /**
     * @var
     */
    public $client_hash;
    /**
     * @var array
     */
    protected $lastResult;
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * ClientActivity constructor.
     * @param Connection $connection
     * @param ClientInterface $client
     */
    public function __construct(Connection $connection, ClientInterface $client)
    {
        parent::__construct($connection);
        $this->client = $client;
        $this->client_id = $client->client_hash[0]['id'];
        $this->client_hash = $client->client_hash[0]['client_hash'];
    }

    /**
     * @return Activity
     */
    public function getEventTypes() : Activity
    {
        $result = $this->_connection->sendGetRequest(self::ACTION_LIST, []);
        $this->fill($result);
        return $this;
    }

    /**
     * @param string $activity_key
     * @param string $activity_datetime
     * @param array $activity_attributes
     * @return Activity
     */
    public function fireEvent(
        string $activityKey,
        string $activityDatetime,
        string $externalId = '',
        array $activityAttributes = []
    ) : Activity
    {
        $data = [
            'client_id' => $this->client_id,
            'client_hash' => $this->client_hash,
            'activity_key' => $activityKey,
            'external_id' => $externalId,
            'activity_created_at' => $activityDatetime,
            'activity_attributes' => $activityAttributes,
        ];
        $result = $this->_connection->sendPostRequest(self::ACTION_FIRE_EVENT, $data);
        $this->fill($result);
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassNameException() : string
    {
        return ClientException::class;
    }
}