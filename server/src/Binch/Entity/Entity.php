<?php

namespace Binch\Entity;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

/**
 * Base class representing a database entity.
 *
 * @package Binch\Entity
 */
class Entity
{
    /**
     * @var OODBBean The bean associated to this entity.
     */
    protected $bean;
    
    /**
     * Default entity constructor.
     */
    protected function __construct()
    {
    }
    
    /**
     * Stores the entity into the database.
     */
    public function save()
    {
        R::store($this->bean);
    }
    
    /**
     * Utility function that formats strings before they get stored into the the database.
     *
     * @param String $string The input string
     * @return string The formatted string
     */
    protected static function format(String $string)
    {
        return preg_replace("/\s+/", " ", trim($string));
    }
}
