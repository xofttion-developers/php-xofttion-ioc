<?php

namespace Xofttion\IoC\Objects;

use Xofttion\IoC\Contracts\IObject;

class ObjectClass implements IObject
{
    // Atributos de la clase ObjectClass

    private string $class;

    // Constructor de la clase ObjectClass

    private function __construct(string $class)
    {
        $this->class = $class;
    }

    // Métodos estáticos de la clase ObjectClass

    public static function build(string $class): self
    {
        return new static($class);
    }

    // Métodos sobrescritos de la intefaz IObject

    public function value()
    {
        return new $this->class();
    }
}
