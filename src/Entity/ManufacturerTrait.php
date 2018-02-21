<?php

namespace Loevgaard\DandomainConsignment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Loevgaard\DandomainStock\Entity\Generated\StockMovementInterface;

trait ManufacturerTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $consignment = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true, length=191)
     */
    protected $consignmentClass;

    /**
     * This is the last stock movement used in reports
     * This implies that stock movements can't be changed
     *
     * @var StockMovementInterface|null
     *
     * @ORM\ManyToOne(targetEntity="Loevgaard\DandomainStock\Entity\StockMovement")
     */
    protected $consignmentLastStockMovement;

    /**
     * @return bool
     */
    public function isConsignment(): bool
    {
        return (bool)$this->consignment;
    }

    /**
     * @param bool $consignment
     * @return ManufacturerTrait
     */
    public function setConsignment(bool $consignment)
    {
        $this->consignment = $consignment;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsignmentClass(): ?string
    {
        return $this->consignmentClass;
    }

    /**
     * @param string|null $consignmentClass
     * @return ManufacturerTrait
     */
    public function setConsignmentClass(?string $consignmentClass)
    {
        $this->consignmentClass = $consignmentClass;
        return $this;
    }

    /**
     * @return StockMovementInterface|null
     */
    public function getConsignmentLastStockMovement(): ?StockMovementInterface
    {
        return $this->consignmentLastStockMovement;
    }

    /**
     * @param StockMovementInterface|null $consignmentLastStockMovement
     * @return ManufacturerTrait
     */
    public function setConsignmentLastStockMovement(?StockMovementInterface $consignmentLastStockMovement)
    {
        $this->consignmentLastStockMovement = $consignmentLastStockMovement;
        return $this;
    }
}