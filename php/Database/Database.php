<?php
namespace Classes\Database;

include_once "MySQLConnection/MySQLConnection.php";

class Database {
    private $dbc = null;
    private $stmt = null;

    public function __construct(DBConnection $conn = null) {
        if($conn === null)
            $conn = new MySQLConnection;
        $this->dbc = $conn->connect();
    }

    public function query($query) {
        $this->stmt = $this->dbc->prepare($query);
    }

    public function bind($param, $value) {
        $this->stmt->bindValue($param, $value);
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }
}
?>
