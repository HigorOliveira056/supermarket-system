<?php
namespace App\Helpers;

use Doctrine\Common\Collections\ArrayCollection;

class GenericCollection extends ArrayCollection {
    
    private string $type;

    private string $msg_error = "Cannot insert value from other types in collection given: ";

    public function __construct(string $type, array $elements = [])
    {
        $this->type = $type;
        if (count($elements) > 1) {
            foreach ($elements as $key => $item) {
                if (!$this->checkType($item)) 
                    throw new \Exception($this->msg_error . gettype($item));
            }
        }
        parent::__construct($elements);
    }

    public function add ($value)  {   
        if (!$this->checkType($value)) throw new \Exception($this->msg_error . gettype($value));
        parent::add($value);
    }

    public function set ($key, $value) {
        if (!$this->checkType($value)) throw new \Exception($this->msg_error . gettype($value));
        parent::set($key, $value);
    }

    public function filter ($calback) {
        $items = [];
        foreach ($this as $key => $item) {
            if ($calback($item)) {
                $items[$key] = $item;
            }
        }
        return new self($this->type, $items);
    }

    protected function checkType ($var) : bool {
        if (is_object($var))
            return $var instanceof $this->type;
        else
            return gettype($var) === $this->type;
    }
}