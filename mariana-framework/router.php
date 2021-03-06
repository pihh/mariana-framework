<?php namespace Mariana\Framework;

use Mariana\Framework\Config;

class Router{

    static protected $uri;
    static protected $uri_size;
    static protected $request;
    static protected $getList = array();
    static protected $postList = array();
    static protected $explodedRequest;
    static public $defaultGet = "/";
    static public $defaultPost = "/";
    static protected $default;
    static public $controllerConstructorParams = array();


    public static function cleanUrlVariable($uri){
        return preg_replace('/{[\s\S]+?}/', '{}', $uri);
    }

    public static function checkForDynamic(){
        self::$uri = explode("/",trim(self::$uri,"/"));
        # estamos a receber j� sem a base portanto queremos
        # Levels:
        $level1 = "/".self::$uri[0]."/{}/";
        $level2 = "/".self::$uri[0]."/".self::$uri[1]."/{}/";

        # Params:
        array_shift(self::$uri);
        $paramsL1 = self::$uri;
        array_shift(self::$uri);
        $paramsL2 = self::$uri;

        # Result Array -> de tr�s para frente porque queremos confirmar primiro o ultimo nivel
        return array(
            $level2 => $paramsL2,
            $level1 => $paramsL1
        );


    }

    public static function get($uri,$actions){
        $uri = self::cleanUrlVariable($uri);
        self::$getList[$uri] = $actions;
    }

    public static function post($uri,$actions){
        $uri = self::cleanUrlVariable($uri);
        self::$postList[$uri] = $actions;
    }

    public static function remakeUri(){
        self::$uri = htmlspecialchars($_SERVER["REQUEST_URI"]);
        self::$uri = ltrim(self::$uri,Config::get("base-route"));

        # Reformula��o do uri;
        if (stripos(self::$uri, "/") === 0) {}else{self::$uri = "/".self::$uri;}
        self::$uri = strtolower(rtrim(self::$uri, '/') . '/');
        return true;
    }

    public static function remakeDefault(){
        self::$default = str_replace("//","/",Config::get("base-route").self::$default);
        return true;
    }

    public static function compareRequest(){
        if(self::$request =="GET"){
            $check = self::$getList;
            self::$default = self::$defaultGet;
        }elseif(self::$request =="POST"){
            $check = self::$postList;
            self::$default = self::$defaultPost;
        }

        # Define the real default
        self::remakeDefault();

        # Caso exista a route na array de
        if(array_key_exists(self::$uri,$check)){
            return $check[self::$uri];
        }else{
            # Neste caso temos de fazer a verificação se há parte dinamica
            $dynamic = self::checkForDynamic();
            foreach($dynamic as $key => $value){
                if(array_key_exists($key,$check)){
                    self::$controllerConstructorParams = $value;
                    return $check[$key];
                }
            }
        }

        header('Location: '.self::$default);

    }

    public static function route(){

        self::remakeUri();
        self::$request = htmlspecialchars($_SERVER["REQUEST_METHOD"]);

        $mvc = self::compareRequest();

        # Choose Controller, Action and Method
        $mvc = self::compareRequest();
        if(array_key_exists("middleware",$mvc)) {
            // Middleware before
            self::runMiddleware($mvc['middleware'],'before');
            // Actual code
            $object_controller = new $mvc["controller"](self::$controllerConstructorParams);
            $object_controller->$mvc["method"]();
            // Middleware after
            self::runMiddleware($mvc['middleware'],'after');
        }else{
            $object_controller = new $mvc["controller"](self::$controllerConstructorParams);
            $object_controller->{$mvc["method"]}();

        }
    }

    private static function runMiddleware($middleware, $when= 'before'){
        // Instanciates the middleware and runs before ( checks if exists in before and runs it )
        foreach((array) $middleware as $m){
            $middlewareObject = new $m[0];
            $middlewareMethod = $m[1];
            (isset($m[2])) ?
                (is_array($m[2])?
                    $middlewareArray = $m[2]:
                    $middlewareArray = array($m[2])):
                $middlewareArray = array();
            $middlewareArray = array($middlewareMethod,$middlewareArray);
            $middlewareObject->{$when}($middlewareArray);
        }

    }
}