<?php

namespace Binch\Util;

/**
 * Class that represents a set of parameters in a request's body.
 *
 * @package Binch\Util
 */
class Params
{
    /**
     * @var array The list of parameters
     */
    private $params;
    
    /**
     * Creates a new set of parameters from JSON formatted data.
     *
     * @param String $json The parameters formatted as JSON
     * @throws HttpError If the JSON data is not valid
     */
    public function __construct(String $json)
    {
        $this->params = json_decode($json, true);
        if($this->params == null)
            throw new HttpError(400, "Request body must be valid JSON");
    }
    
    /**
     * Returns the parameter with the given name.
     *
     * @param String $name The parameter name
     * @return mixed The parameter if found, <code>null</code> otherwise.
     */
    function __get(String $name)
    {
        if(!array_key_exists($name, $this->params))
            return null;
        
        return $this->params[$name];
    }
    
    /**
     * Checks if the parameters are valid according to a list of constraints.
     * This list must associate parameters names with a list of rules.
     * A rule is a string that can either be :
     * <ul>
     *   <li>"<i>required</i>" if the parameter cannot be omitted;</li>
     *   <li>the type that the parameter is required to be.</li>
     * </ul>
     *
     * @param array $constraints
     * @throws HttpError
     */
    public function validate(array $constraints)
    {
        foreach($constraints as $name => $rules)
            foreach($rules as $rule => $value)
            {
                switch($rule)
                {
                    case "required":
                        if($value && empty($this->params[$name]))
                            throw new HttpError(422, "Missing parameter '$name'");
                        break;
                    
                    case "type":
                        if(!empty($this->params[$name]) && !("is_$value")($this->params[$name]))
                            throw new HttpError(422, "Parameter '$name' must be of type '$value'");
                        break;
                    
                    case "array":
                        if(!empty($this->params[$name]))
                        {
                            if(!is_array($this->params[$name]))
                                throw new HttpError(422, "Parameter '$name' must be an array");
                            foreach($this->params[$name] as $pos => $item)
                                if(!("is_$value")($item))
                                    throw new HttpError(422,
                                        "Item at position '$pos' in array '$name' must be of type '$value'");
                        }
                        break;
                }
            }
    }
}
