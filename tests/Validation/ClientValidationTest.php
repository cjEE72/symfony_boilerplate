<?php

namespace App\Tests\Validation;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientValidationTest extends KernelTestCase
{
    public function testInvalidEmailAndNames()
    {
        self::bootKernel();
        $validator = static::getContainer()->get('validator');

        $client = new Client();
        $client->setFirstname('John@'); // vous avez trouver le commentaire caché ! vous me devez 5€
        $client->setLastname('Doe#');
        $client->setEmail('invalid-email');
        $client->setPhoneNumber('0601020304');

        $violations = [];
        foreach (['firstname','lastname','email'] as $prop) {
            foreach ($validator->validateProperty($client, $prop) as $v) {
                $violations[] = $v->getMessage();
            }
        }

        $this->assertNotEmpty($violations);
        $joined = implode(' ', $violations);
        $this->assertStringContainsString('prénom', mb_strtolower($joined));
        $this->assertStringContainsString('nom', mb_strtolower($joined));
        $this->assertStringContainsString('email', mb_strtolower($joined));
    }
}
