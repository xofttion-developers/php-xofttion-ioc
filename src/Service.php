<?php

namespace Xofttion\IoC;

use Xofttion\IoC\Contracts\IFactory;

class Service
{
    // Atributos de la clase Service

    private $factory;

    // Constructor de la clase Service

    private function __construct(IFactory $factory)
    {
        $this->factory = $factory;
    }

    // Métodos estáticos de la clase Service

    public static function build(string $classFactory): self
    {
        return new static(new $classFactory());
    }

    // Métodos de la clase Service

    public function deploy(string $ref)
    {
        $object = $this->factory->build($ref);

        if (is_null($object)) {
            return null;
        }

        return $object->value();
    }
}
