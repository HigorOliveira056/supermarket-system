<?php
namespace App\Domain;

use App\Services\Json;
use App\Services\RequestFactory as Request;

abstract class Entity {
    protected $created_at;
    protected $updated_at;

    public function toJson () : Json {
        return new Json([]);
    }
    public function rules (Request $request) : array {
        return [];
    }
    public function __get ($props) {
        return $this->$props;
    }
    public function __set ($props, $value) {
        $this->$props = $value;
    }
}