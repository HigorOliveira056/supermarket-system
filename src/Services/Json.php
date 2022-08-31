<?php
namespace App\Services;

class Json {
    protected array $json;
    public function __construct(array $json) {
        $this->json = $json;
    }
    public function __toString () {
        return json_encode($this->json, JSON_FORCE_OBJECT) ?: json_encode(['error' => 1], JSON_FORCE_OBJECT);
    }
}