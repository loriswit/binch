<?php

namespace Binch\Entity;

use RedBeanPHP\R;

/**
 * Entity representing a consumer.
 * This is the association of a group member along with how many drinks he consumed.
 *
 * @package Binch\Entity
 */
class Consumer extends Entity
{
    /**
     * Creates and returns a new consumer.
     *
     * @param Member $member The member that has consumed
     * @param int $amount The number of consumed drinks
     * @return Consumer The created consumer
     */
    public static function create(Member $member, int $amount)
    {
        $consumer = new self();
        $consumer->bean = R::dispense("consumer");
        $consumer->bean->member = $member->bean;
        $consumer->bean->amount = $amount;
        
        return $consumer;
    }
}
