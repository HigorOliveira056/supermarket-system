<?php
namespace App\Repository;

use App\Repository\Contracts\ICategoryProductsRepository;
use App\Domain\CategoryProducts;
use App\Domain\CategoryProductsTaxes;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use InfraDataBase\Connection;
use \PDO;

class CategoryProductsRepository implements ICategoryProductsRepository {
    private Connection $conn;
    private $table = "category_product";
    private $tableRelationTaxe = "category_product_taxes";

    public function __construct () {
        $this->connection = new Connection;
    }
    
    public function get(int $idCategory) : ?CategoryProducts {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $conn->prepare($query);
        $statement->execute(['id' => $idCategory]);
        $category = $statement->fetchAll(PDO::FETCH_CLASS, CategoryProducts::class);
        return count($category) < 1 ? null : $category[0];
    }

    public function getAll() : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table}";
        $statement = $conn->query($query);
        $categorys = $statement->fetchAll();
        foreach ($categorys as $key => $item) {
            $categorys[$key] = array_filter($categorys[$key], function ($key) {
                return !is_numeric($key);
            },ARRAY_FILTER_USE_KEY);
        }
        return $statement->rowCount() < 1 ? new ArrayCollection([]) : new ArrayCollection($categorys);
    }

    public function save(CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (name, description, created_at) VALUES
                    (:name, :description, GETDATE())";
        $statement = $conn->prepare($query);
        $statement->execute([
            'name' => $category->name,
            'description' => $category->description,
        ]);
        return $statement->errorCode() !== '';
    }

    public function update(CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "UPDATE {$this->table} SET 
                    name = :name,
                    description = :description,
                    updated_at = GETDATE()
                    WHERE
                        id = :id";
        $statement = $conn->prepare($query);
        $statement->execute([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ]);
        return $statement->errorCode() !== '';
    }

    public function delete(CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $category->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }

    public function saveTaxe(CategoryProductsTaxes $category_taxes) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->tableRelationTaxe} (category_product_id, taxe_id, percentual) 
                    VALUES (:category_product_id, :taxe_id, :percentual)";
        
        $statement = $conn->prepare($query);
        $statement->execute([
            'category_product_id' => $category_taxes->category_id,
            'taxe_id' => $category_taxes->taxe_id,
            'percentual' => $category_taxes->percentual,
        ]);
        return $statement->errorCode() !== '';
    }
}