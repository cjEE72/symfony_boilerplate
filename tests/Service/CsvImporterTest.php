<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CsvImporter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CsvImporterTest extends TestCase
{
    public function testImportCreatesAndUpdatesProducts()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'csv');
        $csv = "id;name;description;price;type;weight;height;width;downloadUrl;licenseKey\n";
        $csv .= ";New Product;New Desc;10.00;physique;1;;1;;;\n";
        $csv .= "42;Existing;Updated Desc;5.00;numerique;;;;;KEY123\n";
        file_put_contents($tmp, $csv);

        $em = $this->createMock(EntityManagerInterface::class);
        $persisted = [];
        $em->method('persist')->willReturnCallback(function ($obj) use (&$persisted) {
            $persisted[] = $obj;
        });
        $em->method('flush')->willReturnCallback(function () {
            // do nothing
        });

        $existingProduct = new Product();
        $existingProduct->setName('Old');

        $repo = $this->createMock(ProductRepository::class);
        $repo->method('find')->willReturnCallback(function ($id) use ($existingProduct) {
            if ($id === 42) {
                return $existingProduct;
            }
            return null;
        });

        $importer = new CsvImporter();
        $result = $importer->importFromPath($tmp, $em, $repo);

        unlink($tmp);

        $this->assertEquals(1, $result['created']);
        $this->assertEquals(1, $result['updated']);
        $this->assertCount(1, $persisted);
        $this->assertSame('New Product', $persisted[0]->getName());
        $this->assertSame('Updated Desc', $existingProduct->getDescription());
    }
}
