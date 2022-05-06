<?php

namespace Xofttion\IoC\Objects;

use ReflectionClass;
use Xofttion\IoC\Contracts\IFactory;
use Xofttion\IoC\Contracts\IObject;

class ObjectInjectable implements IObject
{
    private string $class;

    private IFactory $factory;

    private function __construct(string $class, IFactory $factory)
    {
        $this->class = $class;
        $this->factory = $factory;
    }

    public static function build(string $class, IFactory $factory): self
    {
        return new static($class, $factory);
    }

    public function value()
    {
        $reflectionClass = new ReflectionClass($this->class);

        $constructor = $reflectionClass->getConstructor();

        if (is_null($constructor)) {
            return new $this->class();
        }

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();

            $dependency = $this->factory->build($name);

            if (is_defined($dependency)) {
                $dependencies[] = $dependency->value();
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
