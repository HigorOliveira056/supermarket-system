<?php
namespace App\Domain;

class Client extends Entity {
    protected int $id;
    protected string $name;
    protected string $email;
}