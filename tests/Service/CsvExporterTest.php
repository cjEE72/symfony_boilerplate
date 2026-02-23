<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\CsvExporter;
use PHPUnit\Framework\TestCase;

class CsvExporterTest extends TestCase
{
    public function testExportProducesCsv()
    {
        $p1 = new Product();
        $p1->setName('A');
        $p1->setDescription('Desc A');
        $p1->setPrice('1.00');
        $p1->setType('physique');

        $p2 = new Product();
        $p2->setName('B');
        $p2->setDescription('Desc B');
        $p2->setPrice('2.50');
        $p2->setType('numerique');

        $exporter = new CsvExporter();
        $csv = $exporter->exportProductsToCsv([$p1, $p2]);

        $this->assertStringContainsString('id;name;description;price;type;weight;height;width;downloadUrl;licenseKey', $csv);
        $this->assertStringContainsString('A', $csv);
        $this->assertStringContainsString('Desc B', $csv);
    }
}

