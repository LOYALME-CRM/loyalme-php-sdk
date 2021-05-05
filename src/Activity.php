<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ActivityException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ActivityInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;

class Activity extends Api implements ActivityInterface
{
    const ACTION_LIST = 'activity/list';
    const ACTION_FIRE_EVENT = 'activity/fire-event';

    /**
     * @var integer
     */
    public $client_id;

    /**
     * @var string
     */
    public $client_hash;

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
        $this->client_id = $client->id;
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
     * @param string $activityKey
     * @param string $activityDatetime
     * @param array $activityAttributes
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
        return ActivityException::class;
    }
}
