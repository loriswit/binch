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
     * @param String $description The description of the round
     * @return Round The created round
     */
    public static function create(Group $group, int $price, Member $payer, array $consumers, String $description)
    {
        $round = new self();
        $round->bean = R::dispense("round");
        $round->bean->group = $group->bean;
        $round->bean->price = $price;
        $round->bean->date = R::isoDateTime();
        $round->bean->deleted = false;
        
        if(!empty($description))
            $round->bean->description = self::format($description);
        
        $amount = 0;
        foreach($consumers as $consumer)
        {
            if($consumer->bean->amount < 1)
                continue;
            
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
     * Finds and returns a round by its date and group.
     *
     * @param String $date The round ISO date
     * @param Group $group The group entity
     * @return Round|null The round with the given date if found, <code>null</code> otherwise.
     */
    public static function find(String $date, Group $group)
    {
        $round = new self();
        $round->bean = R::findOne("round", "date = ? AND group_id = ?", [$date, $group->bean->id]);
        return $round->bean == null ? null : $round;
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
     * Marks the round as deleted or not.
     *
     * @param bool $deleted <code>true</code> to delete the round, <code>false</code> to restore
     */
    public function setDeleted(Bool $deleted)
    {
        if($this->bean->deleted == $deleted)
            return;
        
        $this->bean->deleted = $deleted;
        $multiplier = $deleted ? -1 : 1;
        
        $amount = 0;
        foreach($this->bean->sharedConsumerList as $consumer)
        {
            $amount += $consumer->amount;
            $consumer->member->consumed += $this->bean->price * $consumer->amount * $multiplier;
            
            // if one of the consumer is also the payer, make a copy
            if($consumer->member->id == $this->bean->payer->id)
                $this->bean->payer = $consumer->member;
        }
        
        $this->bean->payer->paid += $amount * $this->bean->price * $multiplier;
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
            "payer" => $this->bean->payer->name,
            "description" => $this->bean->description,
            "deleted" => boolval($this->bean->deleted)
        ];
        
        foreach($this->bean->sharedConsumerList as $consumer)
            $data["consumers"][$consumer->member->name] = intval($consumer->amount);
        
        return $data;
    }
}
