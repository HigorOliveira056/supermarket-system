<?php
namespace App\Domain;

use App\Helpers\Json;
use Symfony\Component\Validator\Constraints as Assert;

class Taxes extends Entity {
    protected int $id;
    protected string $name;
    protected float $percentual;

    public function rules () : array {
        return  [
            'name' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Length(['min' => 2, 'max' => 255]),
            ],
            'percentual' => [
                new Assert\NotBlank,
                new Assert\NotNull,
                new Assert\Positive,
            ],
        ];
    }

    public function toJson () : Json {
        return new Json([
            'id' => $this->id,
            'name' => $this->name,
            'percentual' => $this->percentual,
        ]);
    }
}