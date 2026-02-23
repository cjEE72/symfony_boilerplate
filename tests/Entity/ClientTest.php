<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientProperties()
    {
        $c = new Client();
        $c->setFirstname('Alice');
        $c->setLastname('Dupont');
        $c->setEmail('alice@example.com');
        $c->setPhoneNumber('0601020304');
        $c->setAddress('12 rue');

        $this->assertSame('Alice', $c->getFirstname());
        $this->assertSame('Dupont', $c->getLastname());
        $this->assertSame('alice@example.com', $c->getEmail());
        $this->assertSame('0601020304', $c->getPhoneNumber());
        $this->assertSame('12 rue', $c->getAddress());
        $this->assertNotNull($c->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $c->getCreatedAt());
    }
}

