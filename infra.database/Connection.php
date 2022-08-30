<?php
namespace InfraDataBase;

use InfraEnviroment\Env;
use \PDO;
use \Exception;

class Connection {
    private $connection = null;

    const SQL_SRV = "sqlsrv";

    public function __construct () {
        $this->setConnection();
    }

    private function setConnection () : void {
        $type_connection = Env::env("DB_CONNECTION", "sqlsrv");
        $database = Env::env("DB_DATABASE", false) or die("Error: inform the database");
        $host = Env::env("DB_HOST", "locahost");
        $port = Env::env("DB_PORT", "");
        $username = Env::env("DB_USERNAME");
        $password = Env::env("DB_PASSWORD");

        switch ($type_connection) {
            case self::SQL_SRV:
                $connection = "sqlsrv:Server=$host";
                if (!empty($port))
                    $connection .= ",$port";
                $connection .= ";Database=$database";
                try {
                    $this->connection = new PDO($connection, $password, $username);
                    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }catch (\PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            break;
        }
    }

    public function getConnection () : PDO {
        return $this->connection;
    }

    public function beginTransaction () : bool {
        return $this->connection->beginTransaction();
    }

    public function commit () : bool {
        return $this->connection->commit();
    }

    public function rollback () : bool {
        return $this->connection->rollback();
    }
}
