<?php
namespace App\Repository\Contracts;

use App\Domain\CategoryProducts;
use App\Domain\CategoryProductsTaxes;
use Doctrine\Common\Collections\Collection;

interface ICategoryProductsRepository {
    public function get(int $idCategory) : ?CategoryProducts;
    public function getAll() : Collection;
    public function save(CategoryProducts $category) : bool;
    public function update(CategoryProducts $category) : bool;
    public function delete(CategoryProducts $category) : bool;
}