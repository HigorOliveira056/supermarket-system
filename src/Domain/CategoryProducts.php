<?php
namespace App\Domain;

use App\Helpers\GenericCollection;
use App\Helpers\Json;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryProducts extends Entity {
    protected int $id;
    protected string $name;
    protected string $description;

    private GenericCollection $taxes;

    public function __construct () {
        $this->taxes = new GenericCollection(Taxes::class);
    }

    public function addTax (Taxes $tax) : void {
        $this->taxes->add($tax);
    }

    public function removeTax (Taxes $tax) : void {
        $this->taxes = $this->taxes->filter(function ($item) use ($tax) {
            return $item !== $tax;
        });
    }

    public function getTaxes () : GenericCollection {
        return clone $this->taxes;
    }

    public function rules () : array {
        return  [
            'name' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
            'description' => [
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
        ];
    }

    public function toJson () : Json {
        $collection_taxes = new GenericCollection('string');
        foreach ($this->taxes->toArray() as $item) {
            $collection_taxes->add((string) $item->toJson());
        }
        return new Json([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'taxes' => $collection_taxes->toArray(),
        ]);
    }
}