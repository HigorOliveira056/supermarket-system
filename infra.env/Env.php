<?php
namespace InfraEnviroment;

class Env {
    static public function env (string $key, string $default = null) : ?string {
        return array_key_exists($key, $_ENV) ?  $_ENV[$key] : $default;
    }
}