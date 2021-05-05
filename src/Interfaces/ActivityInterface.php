<?php

namespace LoyalmeCRM\LoyalmePhpSdk\Interfaces;

interface ActivityInterface extends ApiInterface
{
    /**
     * @param string $activityKey
     * @param string $activityDatetime
     * @param string $externalId
     * @param array $activityAttributes
     * @return $this
     */
    public function getEventTypes();

    /**
     * @return $this
     */
    public function fireEvent(string $activityKey, string $activityDatetime, string $externalId, array $activityAttributes);
}
