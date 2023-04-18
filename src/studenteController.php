<?php

class studenteController
{

    public function __construct(private studenteGateway $gateway)
    {

    }

    public function processRequest(string $method, ?string $id)
    {
        if($id)
        {
            $this->processResourceRequest($method, $id);
        }
        else
        {
            // deve elencare le alunni
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id):void
    {
        $studente = $this->gateway->getStudente($id);
        
        if(!$studente)
        {
            http_response_code(404);
            echo json_encode(["message"=>"Studente non trovato"]);
            return;
        }

        switch ($method) {
            case 'GET':
                echo json_encode($studente);
                break;

            case 'PATCH':
                #modificare lo studente con id = $id
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);

                if(! empty($errors))
                {
                    http_response_code(422);
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $row = $this->gateway->update($studente, $data);

                echo json_encode(["message"=>"Studente $id modificato correttamente", 
                            "rows"=>$row]);

                break;
            
            case 'DELETE':
                $row = $this->gateway->delete($id);

                echo json_encode(["message"=> "Studente $id è stato eliminato", 
                            "row"=>$row]);

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
                echo json_encode($this->gateway->getAll());
                break;
                
            case 'POST':
                # devo prendere i dati che l'utente che scritto nella richiesta
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if(! empty($errors))
                {
                    http_response_code(422);
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $lastid = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode(["message"=>"studente inserito", "id"=>$lastid]);
                
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
            if (empty($data["nome"])) {
                $errors[] = "nome is required";
            }
            if (empty($data["cognome"])) {
                $errors[] = "cognome is required";
            }
            if (empty($data["codice_fiscale"])) {
                $errors[] = "codice_fiscale is required";
            }
            if (empty($data["data_nascita"])) {
                $errors[] = "data_nascita is required";
            }
            if (empty($data["id_classe"])) {
                $errors[] = "id_classe is required";
            }
        }    

        
        return $errors;
    }


}




?>