<?php
namespace App\Domain;

use App\Helpers\Json;
use Symfony\Component\Validator\Constraints as Assert;

class Product extends Entity {
    protected int $id;
    protected int $category_id;
    protected string $name;
    protected string $description;
    protected float $price;

    protected CategoryProducts $category;

    public function getPrice () :float{
        $taxes = $this->category->getTaxes()->toArray();
        $price = $this->price;
        return $price + array_reduce($taxes, function ($carry, $taxe) use ($price){
            return ($taxe->percentual / 100) * $price + $carry;
        });
    }

    public function getTotalTaxes () : float {
        $taxes = $this->category->getTaxes()->toArray();
        $price = $this->price;
        return array_reduce($taxes, function ($carry, $taxe) use ($price){
            return ($taxe->percentual / 100) * $price + $carry;
        });
    }

    public function percentualTaxes () : float {
        return $this->getTotalTaxes() * 100 / $this->getPrice();
    }

    public function rules () : array {
        return  [
            'category_id' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Positive,
            ],
            'name' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
            'description' => [
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
            'price' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Positive,
            ]
        ];
    }

    public function toJson () : Json {
        return new Json([
            'id' => $this->id,
            'category_id' => $this->category->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => (string) $this->category->toJson()
        ]);
    }
}