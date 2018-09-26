<?php

namespace Binch\Controller;

use Interop\Container\ContainerInterface;

/**
 * Base class for controllers that handle requests.
 *
 * @package Binch\Controller
 */
class Controller
{
    /**
     * @var ContainerInterface The Slim container
     */
    protected $container;
    
    /**
     * Base controller constructor.
     *
     * @param ContainerInterface $container The Slim container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
