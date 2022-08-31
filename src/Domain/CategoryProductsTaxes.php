<?php
namespace App\Domain;

class CategoryProductsTaxes extends Model {
    protected int $category_id;
    protected int $taxe_id;
    protected int $percentual;

    public function rules (Request $request) : array {
        $errors = [];
        $rules = [
            'percentual' => [
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
}