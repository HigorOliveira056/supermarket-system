<?php
namespace App\Domain;

use App\Services\RequestFactory as Request;
use App\Services\Json;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Taxes extends Entity {
    protected int $id;
    protected string $name;
    protected float $percentual;

    public function rules (Request $request) : array {
        $errors = [];
        $rules = [
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
            'name' => $this->name,
            'percentual' => $this->percentual,
        ]);
    }
}