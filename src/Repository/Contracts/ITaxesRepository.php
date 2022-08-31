<?php
namespace App\Repository\Contracts;

use App\Domain\Taxes;
use Doctrine\Common\Collections\Collection;

interface ITaxesRepository {
    public function get(int $idTaxe) : ?Taxes;
    public function getAll() : Collection;
    public function save(Taxes $taxe) : bool;
    public function update(Taxes $taxe) : bool;
    public function delete(Taxes $taxe) : bool;
}