<?php

namespace Xofttion\IoC;

use Xofttion\Kernel\Structs\Json;

class ClassInstance {
    
    // Atributos de la clase ClassInstance
    
    /**
     *
     * @var string 
     */
    private $class;
    
    /**
     *
     * @var Json 
     */
    private $dependences;
    
    // Constructor de la clase ClassInstance
    
    /**
     * 
     * @param string $class
     */
    public function __construct(string $class) {
        $this->dependences = new Json(); $this->setClass($class); 
    }
    
    // Métodos de la clase ClassInstance
    
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
     * @return object
     */
    public function getInstance() {
        return new $this->class();
    }
    
    /**
     * 
     * @param string $key
     * @param string $class
     * @param bool $isShared
     * @return ClassInstance
     */
    public function attach(string $key, string $class, bool $isShared = false): ClassInstance {
        $this->dependences->attach($key, new Dependency($class, $isShared)); return $this;
    }
    
    /**
     * 
     * @return Json
     */
    public function getDependences(): Json {
        return $this->dependences;
    }
}