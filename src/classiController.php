<?php

class classiController
{

    public function __construct(private classeGateway $gateway)
    {

    }

    public function processRequest(string $method, ?string $id, ?string $alunni)
    {
        if($id)
        {
            $this->processResourceRequest($method, $id, $alunni);
        }
        else
        {
            // deve elencare le classi
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id, $alunni):void
    {
        $classe = $this->gateway->get($id);
        if(!$classe)
        {
            http_response_code(404);
            echo json_encode(["message"=> "Classe non trovata!"]);
            return;
        }
        switch ($method) {
            case 'GET':
                if($alunni != "alunni")
                {
                    echo json_encode($classe);
                }
                elseif($alunni == "alunni")
                {
                    $studenti = $this->gateway->getStudente($id);
                    if(!$studenti)
                    {
                        http_response_code(404);
                        echo json_encode(["message"=> "Studenti: 0"]);
                    }
                    else{
                        echo json_encode($studenti);
                    }
                }
                else
                {
                    echo json_encode(["Error"=> "Errore!"]);
                }
                break;

            case 'PATCH':
                # prendere i dati che voglio modificare
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);
                if(!empty($errors))
                {
                    http_response_code(422);
                    echo json_encode(["Errors"=> $errors]);
                    break;
                }

                # funzione di update che ritorna il numero di righe modificate
                
                $rows = $this->gateway->Update($classe, $data);
                echo json_encode(["message" => "Classe $id modificata",
                                "rows"=> $rows]);
                
                break;
            
            case 'DELETE':
                
                #devo andare ad eliminare la classe con id = $id
                $row = $this->gateway->delete($id);

                echo json_encode(["message" => "Classe $id eliminata",
                                "rows"=> $row]);
                
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
                break;
        }
    }
    
    private function processCollectionRequest(string $method):void
    {
        switch ($method) {
            case 'GET':
                # code...
                echo json_encode($this->gateway->getAll());
                break;

            case 'POST':
                # inserire dati nuova classe
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getValidationErrors($data);
                if(!empty($errors))
                {
                    http_response_code(422);
                    echo json_encode(["Errors"=> $errors]);
                    break;
                }

                $id = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode(["message"=>"Classe creata con successo!", "id"=> $id]);

                break;
            
            
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }


    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        if($is_new)
        {
            if (empty($data["anno"])) {
                $errors[] = "anno is required";
            }
            if (empty($data["sezione"])) {
                $errors[] = "sezione is required";
            }
            if (empty($data["spec"])) {
                $errors[] = "spec is required";
            }
        }    
        
        return $errors;
    }


}




?>