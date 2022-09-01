<?php
namespace App\Repository;

use App\Repository\Contracts\ITaxesRepository;
use App\Domain\Taxes;
use Doctrine\Common\Collections\Collection;
use App\Helpers\GenericCollection;
use InfraDataBase\Connection;
use \PDO;

class TaxesRepository implements ITaxesRepository {
    private Connection $conn;
    private string $table = "taxes";

    public function __construct () {
        $this->connection = new Connection;
    }
    
    public function get(int $idTaxe) : ?Taxes {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $conn->prepare($query);
        $statement->execute(['id' => $idTaxe]);
        $taxe = $statement->fetchAll(PDO::FETCH_CLASS, Taxes::class);
        return count($taxe) < 1 ? null : $taxe[0];
    }

    public function getAll() : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table}";
        $statement = $conn->query($query);
        $taxes = $statement->fetchAll(PDO::FETCH_CLASS, Taxes::class);
        return $statement->rowCount() < 1 ? new GenericCollection(Taxes::class, []) : new GenericCollection(Taxes::class, $taxes);
    }

    public function save(Taxes $taxe) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (name, percentual, created_at) VALUES
                    (:name, :percentual, GETDATE())";
        $statement = $conn->prepare($query);
        $conn->beginTransaction();
        try {
            $statement->execute([
                'name' => $taxe->name,
                'percentual' => $taxe->percentual,
            ]);
            $conn->commit();
        }catch (\PDOException $e) {
            $conn->rollback();
            return false;
        }
        return $statement->errorCode() !== '';
    }

    public function update(Taxes $taxe) : bool {
        $conn = $this->connection->getConnection();
        $query = "UPDATE {$this->table} SET 
                    name = :name,
                    percentual = :percentual,
                    updated_at = GETDATE()
                    WHERE
                        id = :id";
        $statement = $conn->prepare($query);
        $conn->beginTransaction();
        try {
            $statement->execute([
                'id' => $taxe->id,
                'name' => $taxe->name,
                'percentual' => $taxe->percentual,
            ]);
            $conn->commit();
        }catch(\PDOException $e) {
            $conn->rollback();
            return false;
        }
        return $statement->errorCode() !== '';
    }

    public function delete(Taxes $taxe) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $taxe->id";
        $conn->beginTransaction();
        try {
            $conn->query($query);
            $conn->commit();
        }catch(\PDOException $e) {
            $conn->rollback();
            return false;
        }
        return $conn->errorCode() !== '';
    }
}