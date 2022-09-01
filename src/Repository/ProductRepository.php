<?php
namespace App\Repository;

use App\Repository\Contracts\IProductRepository;
use App\Domain\Product;
use Doctrine\Common\Collections\Collection;
use App\Helpers\GenericCollection;
use InfraDataBase\Connection;
use \PDO;

class ProductRepository implements IProductRepository{
    private Connection $conn;
    private string $table = "product";

    public function __construct () {
        $this->connection = new Connection;
    }
    
    public function get(int $idProduct) : ?Product {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $conn->prepare($query);
        $statement->execute(['id' => $idProduct]);
        $product = $statement->fetchAll(PDO::FETCH_CLASS, Product::class);
        if ($statement->rowCount() < 1) return null;
        $product[0]->category = (new CategoryProductsRepository)->get($product[0]->category_id);
        return $product[0];
    }

    public function getAll() : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table}";
        $statement = $conn->query($query);
        $products = $statement->fetchAll(PDO::FETCH_CLASS, Product::class);
        $category_repository = new CategoryProductsRepository;
        foreach ($products as $prod) {
            $prod->category = $category_repository->get($prod->category_id);
        }
        
        return $statement->rowCount() < 1 ? new GenericCollection(Product::class, []) : new GenericCollection(Product::class, $products);
    }

    public function save(Product $product) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (category_id, name, description, price, created_at) VALUES
                    (:category_id, :name, :description, :price, GETDATE())";
        $statement = $conn->prepare($query);
        $conn->beginTransaction();
        try {
            $category_id = isset($product->category) ? $product->category->id : $product->category_id;
            $statement->execute([
                'category_id' => $category_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
            ]);
            $conn->commit();
        }catch (\PDOException $e) {
            $conn->rollback();
            return false;
        }
        return $statement->errorCode() !== '';
    }

    public function update(Product $product) : bool {
        $conn = $this->connection->getConnection();
        $query = "UPDATE {$this->table} SET 
                    category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    updated_at = GETDATE()
                    WHERE
                        id = :id";
        $statement = $conn->prepare($query);
        $conn->beginTransaction();
        try {
            $statement->execute([
                'id' => $product->id,
                'category_id' => isset($product->category) ? $product->category->id : $product->category_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
            ]);
            $conn->commit();
        }catch(\PDOException $e) {
            $conn->rollback();
            return false;
        }
        return $statement->errorCode() !== '';
    }

    public function delete(Product $product) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $product->id";
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