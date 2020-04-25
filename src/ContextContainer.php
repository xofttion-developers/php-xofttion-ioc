<?php

namespace Xofttion\IoC;

use Xofttion\Kernel\Structs\DataDictionary;
use Xofttion\IoC\Contracts\IDependencyFactory;

class ContextContainer {
    
    // Atributos de la clase ContextContainer
    
    /**
     *
     * @var ContextContainer 
     */
    protected static $instance;
    
    /**
     *
     * @var ClassBuilder 
     */
    private $builder;

    /**
     *
     * @var IDependencyFactory 
     */
    protected $dependencyFactory;
    
    /**
     *
     * @var DataDictionary 
     */
    private $dependencesFactory;
    
    /**
     *
     * @var DataDictionary 
     */
    private $dependencesShared;

    // Constructor de la clase ContextContainer
    
    private function __construct() {
        $this->dependencesFactory = new DataDictionary();
        $this->dependencesShared  = new DataDictionary();
    }
    
    // Métodos de la clase ContextContainer

    /**
     * 
     * @return ContextContainer
     */
    public static function getInstance(): ContextContainer {
        if (is_null(self::$instance)) {
            self::$instance = new static(); // Instanciando ContextContainer
        } 
        
        return self::$instance; // Retornando instancia
    }
    
    /**
     * 
     * @param string $classFactory
     * @param string $classInstance
     * @return object
     */
    public function create(string $classFactory, string $classInstance) {
        $builder = $this->getFactory($classFactory)->build($classInstance);
        
        if ($builder instanceof ClassInstance) {
            return $this->getBuilder()->create($builder, $this, $classFactory);
        } else {
            return $builder; // Retornando objeto generado
        }
    }
    
    /**
     * 
     * @param string $classDependency
     * @param object $dependency
     * @return void
     */
    public function attachSharedInstance(string $classDependency, $dependency): void {
        $this->dependencesShared->attach($classDependency, $dependency);
    }
    
    /**
     * 
     * @param string $classDependency
     * @return object
     */
    public function getSharedInstance(string $classDependency) {
        return $this->dependencesShared->getValue($classDependency);
    }

    /**
     * 
     * @param string $classFactory
     * @return IDependencyFactory
     */
    private function getFactory(string $classFactory): IDependencyFactory {
        $factory = $this->getDependencyFactory($classFactory); // Factoría
        
        if (is_null($factory)) {
            $factory = new $classFactory(); // Instanciando factoría de dependencias
            $this->attachDependencyFactory($classFactory, $factory);
        } // No existe instancia de la factoría 
        
        return $factory; // Retornando factoría de dependencias contexto
    }
    
    /**
     * 
     * @param string $classFactory
     * @param IDependencyFactory $factory
     * @return void
     */
    private function attachDependencyFactory(string $classFactory, IDependencyFactory $factory): void {
        $this->dependencesFactory->attach($classFactory, $factory);
    }
    
    /**
     * 
     * @param string $classFactory
     * @return IDependencyFactory|null
     */
    private function getDependencyFactory(string $classFactory): ?IDependencyFactory {
        return $this->dependencesFactory->getValue($classFactory);
    }
    
    /**
     * 
     * @return ClassBuilder
     */
    private function getBuilder(): ClassBuilder {
        if (is_null($this->builder)) {
            $this->builder = $this->getInstanceBuilder();
        }
        
        return $this->builder; // Constructor de clase
    }
    
    /**
     * 
     * @return ClassBuilder
     */
    protected function getInstanceBuilder(): ClassBuilder {
        return new ClassBuilder();
    }
}