<?php
namespace App\Repository;

use App\Repository\Contracts\IProductRepository;
use App\Domain\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use InfraDataBase\Connection;
use \PDO;

class ProductRepository implements IProductRepository{
    private Connection $conn;
    private $table = "product";

    public function __construct () {
        $this->connection = new Connection;
    }
    
    public function get(int $idProduct) : ?Product {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $conn->prepare($query);
        $statement->execute(['id' => $idProduct]);
        $product = $statement->fetchAll(PDO::FETCH_CLASS, Product::class);
        return count($product) < 1 ? null : $product[0];
    }

    public function getAll() : Collection {
        $conn = $this->connection->getConnection();
        $query = "SELECT * FROM {$this->table}";
        $statement = $conn->query($query);
        $products = $statement->fetchAll();
        foreach ($products as $key => $item) {
            $products[$key] = array_filter($products[$key], function ($key) {
                return !is_numeric($key);
            },ARRAY_FILTER_USE_KEY);
        }
        return $statement->rowCount() < 1 ? new ArrayCollection([]) : new ArrayCollection($products);
    }

    public function save(Product $product) : bool {
        $conn = $this->connection->getConnection();
        $query = "INSERT INTO {$this->table} (category_id, name, description, price, created_at) VALUES
                    (:category_id, :name, :description, :price, GETDATE())";
        $statement = $conn->prepare($query);
        $statement->execute([
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
        ]);
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
        $statement->execute([
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
        ]);
        return $statement->errorCode() !== '';
    }

    public function delete(Product $product) : bool {
        $conn = $this->connection->getConnection();
        $query = "DELETE FROM {$this->table} WHERE id = $product->id";
        $conn->query($query);
        return $conn->errorCode() !== '';
    }
}