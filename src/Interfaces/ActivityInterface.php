<?php

namespace LoyalmeCRM\LoyalmePhpSdk\Interfaces;

use LoyalmeCRM\LoyalmePhpSdk\Activity;

interface ActivityInterface extends ApiInterface
{
    /**
     * @param string $activityKey
     * @param string $activityDatetime
     * @param string $externalId
     * @param array $activityAttributes
     * @return $this
     */
    public function getEventTypes(): Activity;

    /**
     * @param string $activityKey
     * @param string $activityDatetime
     * @param array $activityAttributes
     * @return Activity
     */
    public function fireEvent(string $activityKey, string $activityDatetime, string $externalId, array $activityAttributes): Activity;
}
