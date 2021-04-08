<?php
    require __DIR__ . "../../Autoload.php";

    define('ROOT_PATH', trim(__DIR__, '/public'));

    return (new \Core\Dispatcher($_SERVER))->run();
?>