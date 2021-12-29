<?php

namespace Xofttion\IoC\Objects;

use Xofttion\IoC\Contracts\IObject;

class ObjectValue implements IObject
{
    // Atributos de la clase ObjectValue

    private $value;

    // Constructor de la clase ObjectValue

    private function __construct($value)
    {
        $this->value = $value;
    }

    // Métodos estáticos de la clase ObjectValue

    public static function build($value): self
    {
        return new static($value);
    }

    // Métodos sobrescritos de la intefaz IObject

    public function value()
    {
        return $this->value;
    }
}
