<?php

namespace Binch\Entity;

use \RedBeanPHP\R as R;

/**
 * Entity that represents a group of members.
 *
 * @package Binch\Entity
 */
class Group extends Entity
{
    /**
     * Creates and returns a new group.
     *
     * @param String $path The group path (used to identify the group)
     * @param String $name The group name
     * @param array $members A list containing the names of the group members
     * @param String $pass The group password (optional)
     * @return Group The created group
     */
    public static function create(String $path, String $name, array $members, String $pass = "")
    {
        $group = new self();
        $group->bean = R::dispense("group");
        $group->bean->path = strtolower($path);
        
        $group->setName($name);
        $group->setMembers($members);
        $group->setPass($pass);
        
        return $group;
    }
    
    /**
     * Finds and returns a group by its path.
     *
     * @param String $path The group path.
     * @return Group|null The group for the given path if found, <code>null</code> otherwise.
     */
    public static function find(String $path)
    {
        $group = new self();
        $group->bean = R::findOne("group", "path = ?", [$path]);
        return $group->bean == null ? null : $group;
    }
    
    /**
     * Defines the group name.
     *
     * @param String $name The new group name
     */
    public function setName(String $name)
    {
        if(!empty($name))
            $this->bean->name = self::format($name);
    }
    
    /**
     * Defines the members list.
     * The list entries will modify the current members name.
     * If the number of entries is higher than the current number of members, new members will be added.
     *
     * @param array $members A list containing the names of the group members
     * @return bool <code>false</code> if there are less list entries than current members,
     * <code>true</code> otherwise
     */
    public function setMembers(array $members)
    {
        $keys = array_keys($this->bean->xownMemberList);
        if(count($members) < count($keys))
            return false;
        
        foreach($members as $index => $name)
        {
            if(empty($name))
                continue;
            
            if(isset($keys[$index]))
                $this->bean->xownMemberList[$keys[$index]]->name = self::format($name);
            
            else
            {
                $member = R::dispense("member");
                $member->name = self::format($name);
                $member->paid = 0;
                $member->consumed = 0;
                $this->bean->xownMemberList[] = Member::create($name)->bean;
            }
        }
        
        return true;
    }
    
    /**
     * Defines a new password.
     * If the password is empty, the group will have no password protection.
     *
     * @param String $pass The new password
     */
    public function setPass(String $pass)
    {
        if(empty($pass))
        {
            $this->bean->pass = null;
            $this->bean->token = null;
        }
        else
        {
            $this->bean->pass = password_hash($pass, PASSWORD_BCRYPT);
            $this->bean->token = md5(uniqid(rand(), true));
        }
    }
    
    /**
     * Tells if the group is protected by a password.
     *
     * @return bool <code>true</code> if it is protected, <code>false</code> otherwise
     */
    public function hasPassword()
    {
        return $this->bean->pass != null;
    }
    
    /**
     * Returns the group's authentication token.
     *
     * @param String $pass The group password
     * @return String|null The token if the group is protected and the password
     * is correct, <code>null</code> otherwise
     */
    public function getToken(String $pass)
    {
        if($this->bean->pass && password_verify($pass, $this->bean->pass))
            return $this->bean->token;
        
        else
            return null;
    }
    
    /**
     * Tells if the given authentication token is valid.
     *
     * @param String $token The authentication token
     * @return bool <code>true</code> if the token is valid or if the group is not
     * protected by a password, <code>false</code> otherwise
     */
    public function authenticate(String $token)
    {
        return $this->bean->token == null || $this->bean->token == $token;
    }
    
    /**
     * Returns the group data as an array.
     *
     * @param bool $withToken when <code>true</code>, the result will include the authentication token
     * @return array An array containing the group properties
     */
    public function export(Bool $withToken = false)
    {
        $data["name"] = $this->bean->name;
        foreach($this->bean->xownMemberList as $member)
            $data["members"][] = [
                "name" => $member->name,
                "paid" => intval($member->paid),
                "consumed" => intval($member->consumed)
            ];
        
        if($withToken && $this->bean->token)
            $data["token"] = $this->bean->token;
        
        return $data;
    }
}
