<?php

#sto usando una notazione tipizzata
declare(strict_types=1);
#per caricare le classi che andro a creare
spl_autoload_register(function ($class)
{
    require __DIR__ . "/src/$class.php";
});

#per gestire le eccezioni
set_exception_handler("ErrorHandler::handleException");

#gestisce l'errore di un posto vuoto senza param
set_error_handler("ErrorHandler::handleError");

#richieste servite in json
header("Content-type:application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
$database = new db("dbserver", "scuola", "root", "ciao");
if(count($parts)>2)
{
    if($parts[2]!= "classi" && $parts[2]!= "alunni")
    {
        http_response_code(404);
        exit;
    }
    if($parts[2] == "classi")
    {

        $id = $parts[3] ?? null;
        $alunni = $parts[4] ?? null;

        $gatewayClassi = new classeGateway($database);
        $controller = new classiController($gatewayClassi);

        $controller->processRequest($_SERVER["REQUEST_METHOD"],$id, $alunni);

    }
    else if($parts[2] == "alunni")
    {
        $id = $parts[3] ?? null;
        
        $gatewayStudente = new studenteGateway($database);
        $controllerStudente = new studenteController($gatewayStudente);

        $controllerStudente->processRequest($_SERVER["REQUEST_METHOD"],$id);

    }
    else
    {
        # messaggio di errore
    }
}



?>