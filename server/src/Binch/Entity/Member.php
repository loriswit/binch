<?php

namespace Binch\Entity;

use RedBeanPHP\R;

/**
 * Entity representing a group member.
 *
 * @package Binch\Entity
 */
class Member extends Entity
{
    /**
     * Creates a new member.
     *
     * @param String $name The member name
     * @return Member The created member
     */
    public static function create(String $name)
    {
        $member = new self();
        $member->bean = R::dispense("member");
        $member->bean->name = $name;
        $member->bean->paid = 0;
        $member->bean->consumed = 0;
        
        return $member;
    }
    
    /**
     * Finds and returns a member by its name and group.
     *
     * @param String $name The member name
     * @param Group $group The group entity
     * @return Member|null The member with the given name if found, <code>null</code> otherwise.
     */
    public static function find(String $name, Group $group)
    {
        $member = new self();
        $member->bean = R::findOne("member", "name = ? AND group_id = ?", [$name, $group->bean->id]);
        return $member->bean == null ? null : $member;
    }
}
