<?php

namespace Xofttion\IoC\Contracts;

interface IDependencyFactory {
    
    // Métodos de la interfaz IDependencyFactory
    
    /**
     * 
     * @param string $class
     * @return object
     */
    public function build(string $class);
}