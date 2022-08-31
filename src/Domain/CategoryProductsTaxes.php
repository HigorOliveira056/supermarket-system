<?php
namespace App\Domain;

use App\Services\RequestFactory as Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryProductsTaxes extends Entity {
    protected int $category_id;
    protected int $taxe_id;

    public function rules (Request $request) : array {
        $errors = [];
        $rules = [
            'taxe_id' => [
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
}