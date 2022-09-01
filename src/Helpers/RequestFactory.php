<?php
namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestFactory {
    protected $request;
    public function __construct () {
        $this->request = SymfonyRequest::createFromGlobals();
    }
    public function get (string $field, $default = null) : ?string {
        return $this->request->get($field, $default);
    }
}