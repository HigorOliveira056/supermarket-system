<?php
namespace App\Repository\Contracts;

use App\Domain\CategoryProducts;
use Doctrine\Common\Collections\Collection;

interface ICategoryProducts {
    public function get(int $idCategory) : ?CategoryProducts;
    public function getAll() : Collection;
    public function save(CategoryProducts $product) : bool;
    public function update(CategoryProducts $product) : bool;
    public function delete(CategoryProducts $product) : bool;
}