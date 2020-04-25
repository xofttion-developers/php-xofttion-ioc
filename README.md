## Koldown IoC

Librería que proporciona un conjunto de clase para implementar los principios de inversión de control (IoC).

## Modo de uso

Lo primero es crear una fábrica `\Koldown\InversionControl\Contracts\IDependenceFactory`, donde se definiran las politicas para instanciar los objetos de clases con sus respectivas dependencias.

    class Factory implements \Koldown\InversionControl\Contracts\IDependenceFactory {
    
        public function build(string $class) {
            switch ($class) {
                case (Sumador::class) :
                    return (new ClassInstance(Sumador::class))->setDependence("operacion", Suma::class);

                case (Restador::class) :
                    return (new ClassInstance(Restador::class))->setDependence("operacion", Resta::class);

                default : return new $class();
            }
        }
    }

En este ejemplo hemos establecido que nuestra fábrica podrá instanciar cualquier clase que se le solicite, sin embargo tenemos dos clases que requieren dependencias para poder realizar su proceso:

    class Sumador implements IOperador {

        private $operacion;

        public function setOperacion(IOperacionAritmetica $operacion): void {
            $this->operacion = $operacion;
        }

        public function ejecutar(int $a, int $b): int {
            return $this->operacion->ejecutar($a, $b);
        }
    }

    class Restador implements IOperador {

        private $operacion;

        public function setOperacion(IOperacionAritmetica $operacion): void {
            $this->operacion = $operacion;
        }

        public function ejecutar(int $a, int $b): int {
            return $this->operacion->ejecutar($a, $b);
        }
    }

Para lograr esto debemos apoyarnos en la clase `Koldown\InversionControl\ClassInstance` que nos permite definir la manera como se construirá una clase con sus respectivas dependencias.

Se debe establer nombre de clave de la dependencia y el tipo de clase a instanciar, donde la clave es un parámetro relevante para realizar esta funcionalidad, por lo que la librería
se basa en los principios de inyección de dependencias usando métodos tipo `setter`, por lo tanto en el ejemplo donde definimos que la clave es "operacion", a la hora de construir el 
objeto se buscará la función "setOperacion" para inyectarle la respectiva dependencia. 

Una vez definido nuestra fábrica de objetos, procedemos aplicar el proceso de IoC en concreto, para esto nos apoyamos en la clase `\Koldown\InversionControl\ContainerContext` que es la
encargada de gestionar la construcción de los objetos de clases solicitados:

    $context  = \Koldown\InversionControl\ContainerContext::getInstance();
    $sumador  = $context->create(Factory::class, Sumador::class);
    $restador = $context->create(Factory::class, Restador::class);
    
    echo "Función suma  (9 + 11) = {$sumador->ejecutar(9, 11)}";
    echo "Función resta (15 - 6) = {$restador->ejecutar(15, 6)}";

Hemos generado instancias de dos funciones aritméticas, sin embargo ahora se requiere encapsular esto en una sola clase llamada `Calculadora`:

    class Calculadora {

        private $sumador;

        private $restador; 

        public function setSumador(IOperador $sumador): void {
            $this->sumador = $sumador;
        }

        public function setRestador(IOperador $restador): void {
            $this->restador = $restador;
        }

        public function sumar(int $a, int $b): int {
            return $this->sumador->ejecutar($a, $b);
        }

        public function restar(int $a, int $b): int {
            return $this->restador->ejecutar($a, $b);
        }
    }

Ahora vamos a nuestra fábrica y agregamos este nuevo caso:

    case (Calculadora::class) :
        return (new ClassInstance(Calculadora::class))
            ->setDependence("restador", Restador::class)
            ->setDependence("sumador", Sumador::class);

Como podemos notar podemos definir las clases parametrizadas anteriormente como dependencias de esta nueva clase, la cual durante su etapa de construcción también inyectará las dependencias de sus dependencias. 

De tal manera que ya podemos ejecutar nuestro programa de la siguiente manera:

    $context     = \Koldown\InversionControl\ContainerContext::getInstance();

    $calculadora = $context->create(Factory::class, Calculadora::class);

    echo "Cálculo suma (4 + 8)  = {$calculadora->sumar(4, 8)}";
    echo "Cálculo resta (9 - 2) = {$calculadora->restar(9, 2)}";

Si desea ver en detalles el código de este ejercicio entre a la carpeta `example` del repositorio y encontrará los ficheros del ejemplo.

## Notas

La función `setDependence` de la clase `Koldown\InversionControl\ClassInstance` recibe un tercer parámetro `$shared` de tipo `bool`, lo cual permite establecer que la instancia de la dependencia construida será 
compartida entre las clases que la soliciten durante la ejecución de creación de objetos desde el `\Koldown\InversionControl\ContainerContext`.