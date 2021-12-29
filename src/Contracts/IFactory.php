<?php

namespace Xofttion\IoC\Contracts;

interface IFactory
{
    // Métodos de la interfaz IFactory

    public function build(string $ref): ?IObject;
}
