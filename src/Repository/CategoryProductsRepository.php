<?php
namespace App\Repository;

use App\Repository\Contracts\ICategoryProductsRepository;
use App\Domain\CategoryProducts;
use App\Domain\Taxes;
use Doctrine\Common\Collections\Collection;
use App\Helpers\GenericCollection;
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
        if ($statement->rowCount() < 1) return null;
        $taxes = $this->getRelationTaxes($category[0]);
        foreach ($taxes as $tax) {
            $category[0]->addTax($tax);
        }
        return $category[0];
    }

    public function getAll() : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table}";
        $statement = $conn->query($query);
        $categorys = $statement->fetchAll(PDO::FETCH_CLASS, CategoryProducts::class);

        foreach ($categorys as $categ) {
            foreach ($this->getRelationTaxes($categ) as $tax) {
                $categ->addTax($tax);
            }
        }

        return $statement->rowCount() < 1 ? 
                new GenericCollection(CategoryProducts::class, []) : new GenericCollection(CategoryProducts::class, $categorys);
    }

    public function save(CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (name, description, created_at) VALUES
                    (:name, :description, GETDATE())";
        $statement = $conn->prepare($query);
        $conn->beginTransaction();
        try {
            $statement->execute([
                'name' => $category->name,
                'description' => $category->description,
            ]);
            $category->id = $conn->lastInsertId();
            foreach ($category->getTaxes()->toArray() as $tax) {
                $this->saveRelationTax($category, $tax);
            }
            $conn->commit();
        }catch (\PDOException $e) {
            $conn->rollback();
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
        $conn->beginTransaction();
        try {
            $statement->execute([
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
            ]);
            $this->deleteRelationTaxes($category);
            foreach ($category->getTaxes()->toArray() as $tax) {
                $this->saveRelationTax($category, $tax);
            }
            $conn->commit();
        }catch (\PDOException $e) {
            $conn->rollback();
        }
        return $statement->errorCode() !== '';
    }

    public function delete(CategoryProducts $category) : bool {
        $this->deleteRelationTaxes($category);
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $category->id";
        $conn->beginTransaction();
        try {
            $conn->query($query);
            $conn->commit();
        }catch (\PDOException $e) {
            $conn->rollback();
        }
        return $conn->errorCode() !== '';
    }

    protected function getRelationTaxes (CategoryProducts $category) : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT t.* FROM taxes t
                    INNER JOIN {$this->tablePivotTax} r ON r.taxe_id = t.id
                    WHERE r.category_product_id = {$category->id}";
        $statement = $conn->query($query);
        $taxe = $statement->fetchAll(PDO::FETCH_CLASS, Taxes::class);  
        return $statement->rowCount() < 1 ? new GenericCollection(Taxes::class, []) : new GenericCollection(Taxes::class, $taxe);
    }

    protected function deleteRelationTaxes (CategoryProducts $category) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->tablePivotTax} WHERE category_product_id = $category->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }

    protected function saveRelationTax (CategoryProducts $category, Taxes $taxes) : bool {
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