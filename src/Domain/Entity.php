<?php
namespace App\Domain;

use App\Helpers\Json;

abstract class Entity {
    protected $created_at;
    protected $updated_at;

    public function toJson () : Json {
        return new Json([]);
    }
    public function rules () : array {
        return [];
    }
    public function __get ($props) {
        return $this->$props;
    }
    public function __set ($props, $value) {
        $reflectionProperty = new \ReflectionProperty($this,$props);
        if (!$reflectionProperty->isPrivate())
            $this->$props = $value;
    }
}