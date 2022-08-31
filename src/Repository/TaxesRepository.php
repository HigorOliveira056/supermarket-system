<?php
namespace App\Repository;

use App\Repository\Contracts\ITaxesRepository;
use App\Domain\Taxes;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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
        $taxes = $statement->fetchAll();
        foreach ($taxes as $key => $item) {
            $taxes[$key] = array_filter($taxes[$key], function ($key) {
                return !is_numeric($key);
            },ARRAY_FILTER_USE_KEY);
        }
        return $statement->rowCount() < 1 ? new ArrayCollection([]) : new ArrayCollection($taxes);
    }

    public function save(Taxes $taxe) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (name, percentual, created_at) VALUES
                    (:name, :percentual, GETDATE())";
        $statement = $conn->prepare($query);
        $statement->execute([
            'name' => $taxe->name,
            'percentual' => $taxe->percentual,
        ]);
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
        $statement->execute([
            'id' => $taxe->id,
            'name' => $taxe->name,
            'percentual' => $taxe->percentual,
        ]);
        return $statement->errorCode() !== '';
    }

    public function delete(Taxes $taxe) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $taxe->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }
}