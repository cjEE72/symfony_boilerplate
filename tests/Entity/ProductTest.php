<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductProperties()
    {
        $p = new Product();
        $p->setName('Test');
        $p->setDescription('Desc');
        $p->setPrice('12.34');
        $p->setType('physique');
        $p->setWeight(1.23);
        $p->setHeight(10.0);
        $p->setWidth(5.0);
        $p->setDownloadUrl('https://example.com');
        $p->setLicenseKey('KEY123');

        $this->assertSame('Test', $p->getName());
        $this->assertSame('Desc', $p->getDescription());
        $this->assertSame('12.34', $p->getPrice());
        $this->assertSame('physique', $p->getType());
        $this->assertSame(1.23, $p->getWeight());
        $this->assertSame(10.0, $p->getHeight());
        $this->assertSame(5.0, $p->getWidth());
        $this->assertSame('https://example.com', $p->getDownloadUrl());
        $this->assertSame('KEY123', $p->getLicenseKey());
    }
}

