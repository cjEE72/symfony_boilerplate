<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserProperties()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('hashed');
        $user->setFirstname('John');
        $user->setLastname('Doe');

        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertSame('hashed', $user->getPassword());
        $this->assertSame('John', $user->getFirstname());
        $this->assertSame('Doe', $user->getLastname());
        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }
}

