<?php
namespace App\Repository\Contracts;

use App\Domain\Product;
use Doctrine\Common\Collections\Collection;

interface IProductRepository {
    public function get(int $idProduct) : ?Product;
    public function getAll() : Collection;
    public function save(Product $product) : bool;
    public function update(Product $product) : bool;
    public function delete(Product $product) : bool;
}