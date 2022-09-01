<?php
namespace App\Repository;

use App\Repository\Contracts\ICategoryProductsRepository;
use App\Domain\CategoryProducts;
use App\Domain\Taxes;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use InfraDataBase\Connection;
use \PDO;

class CategoryProductsRepository implements ICategoryProductsRepository {
    private Connection $conn;
    private string $table = "category_product";
    private string $tablePivotTax = "category_product_taxes";

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
        $category->id = $conn->lastInsertId();
        foreach ($category->getTaxes()->toArray() as $tax) {
            $this->saveRelationTax($category, $tax);
        }
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
        $this->deleteRelationTaxes($category);
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $category->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }

    public function getRelationTaxes (CategoryProducts $category) : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT t.* FROM taxes t
                    INNER JOIN {$this->tablePivotTax} r ON r.taxe_id = t.id
                    WHERE r.category_product_id = {$category->id}";
        $statement = $conn->query($query);
        $taxe = $statement->fetchAll(PDO::FETCH_CLASS, Taxes::class);  
        return $statement->rowCount() < 1 ? new ArrayCollection([]) : new ArrayCollection($taxe);
    }

    public function deleteRelationTaxes (CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->tablePivotTax} WHERE category_product_id = $category->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }

    public function saveRelationTax (CategoryProducts $category, Taxes $taxes) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->tablePivotTax} (category_product_id, taxe_id) 
                    VALUES (:category_product_id, :taxe_id)";
        
        $statement = $conn->prepare($query);
        $statement->execute([
            'category_product_id' => $category->id,
            'taxe_id' => $taxes->id,
        ]);
        return $statement->errorCode() !== '';
    }
   
}