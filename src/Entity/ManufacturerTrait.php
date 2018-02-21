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