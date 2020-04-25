<?php

namespace Xofttion\IoC;

class Dependency {
    
    // Atributos de la clase Dependency
    
    /**
     *
     * @var string 
     */
    private $class;
    
    /**
     *
     * @var bool 
     */
    private $shared;
    
    // Constructor de la clase Dependency
    
    /**
     * 
     * @param string $class
     * @param bool $shared
     */
    public function __construct(string $class, bool $shared = false) {
        $this->setClass($class); $this->setShared($shared);
    }
    
    // Métodos de la clase Dependency
    
    /**
     * 
     * @param string $class
     * @return void
     */
    public function setClass(string $class): void {
        $this->class = $class;
    }
    
    /**
     * 
     * @return string
     */
    public function getClass(): string {
        return $this->class;
    }
    
    /**
     * 
     * @param bool $shared
     * @return void
     */
    public function setShared(bool $shared): void {
        $this->shared = $shared;
    }
    
    /**
     * 
     * @return bool
     */
    public function isShared(): bool {
        return $this->shared;
    }
}