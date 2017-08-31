<?php

namespace fiunchinho\Silex;

use Pimple\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ScopeInterface;

class PimpleInteropWrapper implements ContainerInterface
{
    protected $pimple;

    public function __construct(Container $pimple)
    {
        $this->pimple = $pimple;
    }

    public function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        $this->pimple->offsetSet($id, $service);
    }

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->pimple->offsetGet($id);
    }

    public function has($id)
    {
        return $this->pimple->offsetExists($id);
    }

    public function getParameter($name)
    {
        return $this->pimple->offsetGet($name);
    }

    public function hasParameter($name)
    {
        return $this->pimple->offsetExists($name);
    }

    public function setParameter($name, $value)
    {
        $this->pimple->offsetSet($name, $value);
    }

    public function enterScope($name)
    {
        // TODO: Implement enterScope() method.
    }

    public function leaveScope($name)
    {
        // TODO: Implement leaveScope() method.
    }

    public function addScope(ScopeInterface $scope)
    {
        // TODO: Implement addScope() method.
    }

    public function hasScope($name)
    {
        return $name === self::SCOPE_CONTAINER;
    }

    public function isScopeActive($name)
    {
        return $name === self::SCOPE_CONTAINER;
    }
}
