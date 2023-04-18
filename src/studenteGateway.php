
<?php


class studenteGateway
{
    private PDO $conn;
    
    public function __construct(db $db)
    {
        $this->conn = $db->getConnection();
    }
    
    public function getAll(): array
    {
        # prendo tutte le righe dalla tabella Classe
        $sql = "SELECT * FROM ALUNNO";
                
        $stmt = $this->conn->query($sql);
        
        $data = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {            
            $data[] = $row;
        }
        
        return $data;
    }

    public function getStudente($id): array | false
    {
        # prendo tutte le righe dalla tabella Classe
        $sql = "SELECT * FROM ALUNNO WHERE id = :id";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }

    public function create(array $data):string
    {
        $sql = "INSERT INTO ALUNNO (nome, cognome, codice_fiscale, data_nascita, id_classe) VALUES (:nome, :cognome, :cf, :dn, :idc)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":nome", $data["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":cognome", $data["cognome"], PDO::PARAM_STR);
        $stmt->bindValue(":cf", $data["codice_fiscale"], PDO::PARAM_STR);
        $stmt->bindValue(":dn", $data["data_nascita"], PDO::PARAM_STR);
        $stmt->bindValue(":idc", $data["id_classe"], PDO::PARAM_INT);

        $stmt->execute();

        return $this->conn->lastInsertId();

    }



    public function update(array $current, array $new): int
    {
        $sql = "UPDATE ALUNNO 
                SET nome = :nome, cognome= :cognome, codice_fiscale = :cf, 
                data_nascita = :dn, id_classe=:idc
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);


        $stmt->bindValue(":nome", $new["nome"]?? $current["nome"], PDO::PARAM_STR);
        $stmt->bindValue(":cognome", $new["cognome"]?? $current["cognome"], PDO::PARAM_STR);
        $stmt->bindValue(":cf", $new["codice_fiscale"]?? $current["codice_fiscale"], PDO::PARAM_STR);
        $stmt->bindValue(":dn", $new["data_nascita"]?? $current["data_nascita"], PDO::PARAM_STR);
        $stmt->bindValue(":idc", $new["id_classe"]?? $current["id_classe"], PDO::PARAM_INT);

    
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id):int
    {
        $sql="DELETE FROM ALUNNO WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
   


}


?>