<?php

namespace Loevgaard\DandomainConsignment\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ProductTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $validBarCode = false;

    /**
     * @return bool
     */
    public function isValidBarCode(): bool
    {
        return $this->validBarCode;
    }

    /**
     * @param bool $validBarCode
     * @return ProductTrait
     */
    public function setValidBarCode(bool $validBarCode)
    {
        $this->validBarCode = $validBarCode;
        return $this;
    }
}