<?php
namespace App\Controllers;

use Symfony\Component\Validator\Validation;
use App\Helpers\RequestFactory as Request;

abstract class BaseController {
    static public function validate (array $rules, Request $request) {
        $errors = [];
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