<?php

namespace Binch\Entity;

use RedBeanPHP\R;

/**
 * Entity representing a round of drinks.
 *
 * @package Binch\Entity
 */
class Round extends Entity
{
    /**
     * Creates a new round.
     *
     * @param Group $group The group of the round
     * @param int $price The price of a single drink
     * @param Member $payer The member that payed
     * @param array $consumers A list of the members that consumed
     * @return Round The created round
     */
    public static function create(Group $group, int $price, Member $payer, array $consumers)
    {
        $round = new self();
        $round->bean = R::dispense("round");
        $round->bean->group = $group->bean;
        $round->bean->price = $price;
        $round->bean->date = R::isoDateTime();
        
        $amount = 0;
        foreach($consumers as $consumer)
        {
            $amount += $consumer->bean->amount;
            $consumer->bean->member->consumed += $price * $consumer->bean->amount;
            $round->bean->sharedConsumerList[] = $consumer->bean;
            
            // if one of the consumer is also the payer, make a copy
            if($consumer->bean->member->id == $payer->bean->id)
                $round->bean->payer = $consumer->bean->member;
        }
        
        if(!isset($round->bean->payer))
            $round->bean->payer = $payer->bean;
        
        $round->bean->payer->paid += $amount * $price;
        
        return $round;
    }
    
    /**
     * Finds and returns all rounds for a given group.
     *
     * @param Group $group The group entity
     * @return array A list of all the rounds
     */
    public static function findAll(Group $group)
    {
        $rounds = [];
        foreach($group->bean->ownRoundList as $roundBean)
        {
            $round = new self();
            $round->bean = $roundBean;
            $rounds[] = $round;
        }
        
        return $rounds;
    }
    
    /**
     * Returns the round data as an array.
     *
     * @return array An array containing the round properties
     */
    public function export()
    {
        $data = [
            "date" => $this->bean->date,
            "price" => intval($this->bean->price),
            "payer" => $this->bean->payer->name
        ];
        
        foreach($this->bean->sharedConsumerList as $consumer)
            $data["consumers"][$consumer->member->name] = $consumer->amount;
        
        return $data;
    }
}
