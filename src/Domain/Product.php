<?php
namespace App\Domain;

use App\Services\RequestFactory as Request;
use App\Services\Json;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Product extends Entity {
    protected int $id;
    protected int $category_id;
    protected string $name;
    protected string $description;
    protected float $price; 

    public function rules (Request $request) : array {
        $errors = [];
        $rules = [
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
        $validator = Validation::createValidator();
        foreach ($rules as $key => $item) {
            $hasError = $validator->validate($request->get($key), $item);
            if (count($hasError) > 0) {
                foreach ($hasError as $violation) {
                    $errors[$key][] = $violation->getMessage();
                }
            }
        }
        return $errors;
    }

    public function toJson () : Json {
        return new Json([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ]);
    }

    public function __get ($props) {
        return $this->$props;
    }
    public function __set ($props, $value) {
        $this->$props = $value;
    }
}