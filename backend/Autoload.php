<?php

ob_start();

class Autoload 
{
    protected const EXTENSION = [
        '.php'
    ];
    
    private $namespaces = [];

    public function load()
    {
       foreach (self::EXTENSION as $ext) {
           return spl_autoload_register(function($class) use ($ext) {
               return $this->requireClasses($class, $ext);
           });
       }

       throw new \RuntimeException(sprintf("%s is not valid extension.", $ext));
    }

    private function requireClasses($class, $ext)
    {
        $filename = __DIR__ . "/" . str_replace("\\", "/", $class) . $ext;

        if (file_exists($filename)) {
            require $filename;
            $this->namespaces[] = $filename;
            return;
        }

        foreach (glob(dirname($filename) . "/*") as $file) {

            if ($filename == $file) {

                require $file;
                $this->namespaces[] = $filename;
                return;
            }

        }

        $this->ClassNotFound($class);
    }

    public function ClassNotFound($class)
    {
        header("HTTP/1.0 404 Not Found");

        ob_clean();
        ob_flush();

        die(json_encode([
            "status" => http_response_code(404),
            "description" => $class ." Class Not Found."
        ]));
    }

    public function getNamespace()
    {
        return $this->namespaces;
    }
}

(new Autoload())->load();