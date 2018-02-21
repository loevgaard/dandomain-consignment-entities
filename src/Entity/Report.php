<?php declare(strict_types=1);

namespace Loevgaard\DandomainConsignment\Entity;

use Assert\Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Loevgaard\DandomainConsignment\Entity\Generated\ReportInterface;
use Loevgaard\DandomainConsignment\Entity\Generated\ReportTrait;
use Loevgaard\DandomainFoundation\Entity\Generated\ManufacturerInterface;
use Loevgaard\DandomainStock\Entity\Generated\StockMovementInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as FormAssert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ldc_reports")
 * @ORM\HasLifecycleCallbacks()
 */
class Report implements ReportInterface
{
    use ReportTrait;
    use Timestampable;

    const STATUS_ERROR = 'error';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var ManufacturerInterface
     *
     * @FormAssert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Loevgaard\DandomainFoundation\Entity\Manufacturer")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    protected $manufacturer;

    /**
     * @var string
     *
     * @FormAssert\Choice(callback="getStatuses")
     *
     * @ORM\Column(type="string", length=191)
     */
    protected $status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $error;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    protected $file;

    /**
     * @var StockMovementInterface[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Loevgaard\DandomainStock\Entity\StockMovement")
     * @ORM\JoinTable(name="ldc_reports_stock_movements")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    protected $stockMovements;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->stockMovements = new ArrayCollection();
    }

    public function markAsError(?string $error = null) : void
    {
        $this->status = self::STATUS_ERROR;
        $this->error = $error;
    }

    public function markAsSuccess() : void
    {
        $this->status = self::STATUS_SUCCESSFUL;
        $this->error = null;
    }

    public function isStatus(string $status) : bool
    {
        return $this->status === $status;
    }

    public function isSuccessful() : bool
    {
        return $this->isStatus(self::STATUS_SUCCESSFUL);
    }

    public function isError() : bool
    {
        return $this->isStatus(self::STATUS_ERROR);
    }

    /**
     * Returns true if the report can be delivered to the consignor
     *
     * @return bool
     */
    public function isDeliverable() : bool
    {
        return $this->isSuccessful();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function validate() : void
    {
        Assert::that($this->manufacturer)->isInstanceOf(ManufacturerInterface::class);
        Assert::that($this->status)->choice(self::getStatuses());
        Assert::thatNullOr($this->error)->string();
        Assert::thatNullOr($this->file)->string()->maxLength(191);
        Assert::thatAll($this->stockMovements->toArray())->isInstanceOf(StockMovementInterface::class);
    }

    public static function getStatuses() : array
    {
        return [
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_SUCCESSFUL => self::STATUS_SUCCESSFUL,
            self::STATUS_ERROR => self::STATUS_ERROR
        ];
    }

    public function addStockMovement(StockMovementInterface $stockMovement) : ReportInterface
    {
        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
        }

        return $this;
    }

    public function removeStockMovement(StockMovementInterface $stockMovement) : ReportInterface
    {
        $this->stockMovements->removeElement($stockMovement);

        return $this;
    }

    public function clearStockMovements() : ReportInterface
    {
        $this->stockMovements->clear();

        return $this;
    }

    /**
     * Returns the total for all stock movements
     * Returns a Money object with amount = 0 and $defaultCurrency if there are no stock movements
     *
     * @param string $defaultCurrency
     * @return Money
     * @throws \Loevgaard\DandomainStock\Exception\UnsetCurrencyException
     */
    public function getTotal(string $defaultCurrency = 'DKK') : Money
    {
        $total = null;

        foreach ($this->stockMovements as $stockMovement) {
            if (!$total) {
                $total = new Money(0, new Currency($stockMovement->getCurrency()));
            }

            $total = $total->add($stockMovement->getTotalPrice());
        }

        if (!$total) {
            $total = new Money(0, new Currency($defaultCurrency));
        }

        return $total;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return ReportInterface
     */
    public function setId(int $id) : ReportInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ManufacturerInterface
     */
    public function getManufacturer(): ?ManufacturerInterface
    {
        return $this->manufacturer;
    }

    /**
     * @param ManufacturerInterface $manufacturer
     * @return ReportInterface
     */
    public function setManufacturer(ManufacturerInterface $manufacturer) : ReportInterface
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ReportInterface
     */
    public function setStatus(string $status) : ReportInterface
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param null|string $error
     * @return ReportInterface
     */
    public function setError(?string $error) : ReportInterface
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile(): ?\SplFileInfo
    {
        if(!$this->file) {
            return null;
        }

        return new \SplFileInfo($this->file);
    }

    /**
     * @param \SplFileInfo $file
     * @return Report
     */
    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file->getPathname();
        return $this;
    }

    /**
     * @return StockMovementInterface[]|ArrayCollection
     */
    public function getStockMovements() : Collection
    {
        return $this->stockMovements;
    }

    /**
     * @param StockMovementInterface[]|Collection $stockMovements
     * @return ReportInterface
     */
    public function setStockMovements(Collection $stockMovements) : ReportInterface
    {
        foreach ($stockMovements as $stockMovement) {
            $this->addStockMovement($stockMovement);
        }

        return $this;
    }
}
