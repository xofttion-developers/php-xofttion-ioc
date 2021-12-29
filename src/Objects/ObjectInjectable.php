<?php

namespace Xofttion\IoC\Objects;

use ReflectionClass;
use Xofttion\IoC\Contracts\IFactory;
use Xofttion\IoC\Contracts\IObject;

class ObjectInjectable implements IObject
{
    // Atributos de la clase ObjectInjectable

    private $class;

    private $factory;

    // Constructor de la clase ObjectInjectable

    private function __construct(string $class, IFactory $factory)
    {
        $this->class = $class;
        $this->factory = $factory;
    }

    // Métodos estáticos de la clase ObjectInjectable

    public static function build(string $class, IFactory $factory): self
    {
        return new static($class, $factory);
    }

    // Métodos sobrescritos de la intefaz IObject

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
