
<?php


class classeGateway
{
    private PDO $conn;
    
    public function __construct(db $db)
    {
        $this->conn = $db->getConnection();
    }
    
    public function getAll(): array
    {
        # prendo tutte le righe dalla tabella Classe
        $sql = "SELECT * FROM CLASSE";
                
        $stmt = $this->conn->query($sql);
        
        $data = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {            
            $data[] = $row;
        }
        
        return $data;
    }

    public function get(string $id):array | false
    {
        $sql = "SELECT * FROM CLASSE WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data  = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
    public function getStudente(string $id):array | false
    {
        $sql = "SELECT distinct ALUNNO.* FROM `ALUNNO` INNER JOIN `CLASSE` WHERE ALUNNO.id_classe = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        

        $data = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {            
            $data[] = $row;
        }
        
        return $data;
    }

    public function create(array $data):string
    {
        #Funzione di creazione di una classe
        $sql = "INSERT INTO CLASSE (anno, sezione, spec) VALUES (:anno, :sezione, :spec)";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":anno", $data["anno"]?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":sezione", $data["sezione"] , PDO::PARAM_STR);
        $stmt->bindValue(":spec", $data["spec"] , PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE CLASSE 
                SET anno = :anno, sezione= :sezione, spec = :spec
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":anno", $new["anno"]?? $current["anno"], PDO::PARAM_INT);
        $stmt->bindValue(":sezione", $new["sezione"]?? $current["sezione"] , PDO::PARAM_STR);
        $stmt->bindValue(":spec", $new["spec"]?? $current["spec"], PDO::PARAM_STR);

        #per effettuare l'update prendo l'id dal current[id]
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id):int
    {
        $sql="DELETE FROM CLASSE WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


}


?>