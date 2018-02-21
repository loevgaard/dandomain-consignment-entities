<?php declare(strict_types=1);

namespace Loevgaard\DandomainConsignment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Loevgaard\DandomainFoundation\Entity\Manufacturer;
use Loevgaard\DandomainStock\Entity\StockMovement;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    public function testGettersSetters()
    {
        $manufacturer = new Manufacturer();
        $stockMovements = new ArrayCollection([
            new StockMovement()
        ]);

        $file = new \SplFileInfo('test');

        $report = new Report();
        $report->setId(1)
            ->setStatus(Report::STATUS_SUCCESSFUL)
            ->setManufacturer($manufacturer)
            ->setError('error')
            ->setFile($file)
            ->setStockMovements($stockMovements)
        ;

        $report->validate();

        $this->assertSame(1, $report->getId());
        $this->assertSame(Report::STATUS_SUCCESSFUL, $report->getStatus());
        $this->assertSame($manufacturer, $report->getManufacturer());
        $this->assertSame('error', $report->getError());
        $this->assertEquals($file, $report->getFile());
        $this->assertEquals($stockMovements, $report->getStockMovements());
    }

    public function testMarkAsSuccess()
    {
        $report = new Report();
        $report->setError('error');
        $report->markAsSuccess();

        $this->assertSame(Report::STATUS_SUCCESSFUL, $report->getStatus());
        $this->assertNull($report->getError());
    }

    public function testMarkAsError()
    {
        $report = new Report();
        $report->markAsError('error');

        $this->assertSame(Report::STATUS_ERROR, $report->getStatus());
        $this->assertSame('error', $report->getError());
    }

    public function testIsError()
    {
        $report = new Report();
        $this->assertFalse($report->isError());

        $report->markAsError('error');

        $this->assertTrue($report->isError());
    }

    public function testIsSuccess()
    {
        $report = new Report();
        $this->assertFalse($report->isSuccessful());

        $report->markAsSuccess();

        $this->assertTrue($report->isSuccessful());
    }

    public function testIsDeliverable()
    {
        $report = new Report();

        $this->assertFalse($report->isDeliverable());
    }

    /**
     * @throws \Loevgaard\DandomainStock\Exception\CurrencyMismatchException
     * @throws \Loevgaard\DandomainStock\Exception\UnsetCurrencyException
     */
    public function testGetTotal()
    {
        $report = new Report();

        // add first stock movement
        $stockMovement = new StockMovement();
        $stockMovement->setQuantity(1)->setPrice(new Money(100, new Currency('DKK')));
        $report->addStockMovement($stockMovement);

        // add second stock movement
        $stockMovement = new StockMovement();
        $stockMovement->setQuantity(2)->setPrice(new Money(200, new Currency('DKK')));
        $report->addStockMovement($stockMovement);

        $this->assertEquals(new Money(500, new Currency('DKK')), $report->getTotal());
    }

    /**
     * @throws \Loevgaard\DandomainStock\Exception\UnsetCurrencyException
     */
    public function testGetTotalWithNoStockMovements()
    {
        $report = new Report();

        $this->assertEquals(new Money(0, new Currency('USD')), $report->getTotal('USD'));
    }

    /**
     * @throws \Loevgaard\DandomainStock\Exception\CurrencyMismatchException
     */
    public function testClearAndRemoveStockMovements()
    {
        $report = new Report();

        // add first stock movement
        $stockMovement1 = new StockMovement();
        $stockMovement1->setQuantity(1)->setPrice(new Money(100, new Currency('DKK')));
        $report->addStockMovement($stockMovement1);

        // add second stock movement
        $stockMovement2 = new StockMovement();
        $stockMovement2->setQuantity(2)->setPrice(new Money(200, new Currency('DKK')));
        $report->addStockMovement($stockMovement2);

        $report->removeStockMovement($stockMovement2);

        $this->assertSame($stockMovement1, $report->getStockMovements()->toArray()[0]);

        $report->clearStockMovements();

        $this->assertCount(0, $report->getStockMovements());
    }

    public function testGetStatuses()
    {
        $this->assertSame([
            Report::STATUS_PENDING => Report::STATUS_PENDING,
            Report::STATUS_SUCCESSFUL => Report::STATUS_SUCCESSFUL,
            Report::STATUS_ERROR => Report::STATUS_ERROR
        ], Report::getStatuses());
    }
}
