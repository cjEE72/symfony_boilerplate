<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setFirstname('Jean');
        $admin->setLastname('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password123'));
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@test.com');
        $managerUser->setFirstname('Sophie');
        $managerUser->setLastname('Manager');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword($this->hasher->hashPassword($managerUser, 'password123'));
        $manager->persist($managerUser);

        $standardUser = new User();
        $standardUser->setEmail('user@test.com');
        $standardUser->setFirstname('Luc');
        $standardUser->setLastname('Utilisateur');
        $standardUser->setRoles(['ROLE_USER']);
        $standardUser->setPassword($this->hasher->hashPassword($standardUser, 'password123'));
        $manager->persist($standardUser);

        $client1 = new Client();
        $client1->setFirstname('Alice');
        $client1->setLastname('Dupont');
        $client1->setEmail('alice.dupont@example.com');
        $client1->setPhoneNumber('0601020304');
        $client1->setAddress('12 rue de la Paix, Paris');
        $manager->persist($client1);

        $client2 = new Client();
        $client2->setFirstname('Bob');
        $client2->setLastname('Martin');
        $client2->setEmail('bob.martin@example.com');
        $client2->setPhoneNumber('0605060708');
        $client2->setAddress('45 avenue des Champs, Lyon');
        $manager->persist($client2);

        $client3 = new Client();
        $client3->setFirstname('Carla');
        $client3->setLastname('Nguyen');
        $client3->setEmail('carla.nguyen@example.com');
        $client3->setPhoneNumber('0612345678');
        $client3->setAddress('8 boulevard Victor, Marseille');
        $manager->persist($client3);

        $product1 = new Product();
        $product1->setName('Montre connectée');
        $product1->setDescription('Montre connectée avec suivi d\'activité');
        $product1->setPrice('199.99');
        $product1->setType('physique');
        $product1->setWeight(0.05);
        $product1->setHeight(4.0);
        $product1->setWidth(4.0);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('E-book Symfony');
        $product2->setDescription('Guide complet pour Symfony 6');
        $product2->setPrice('9.99');
        $product2->setType('numerique');
        $product2->setDownloadUrl('https://example.com/download/ebook-symfony');
        $product2->setLicenseKey('LIC-EBK-001');
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Casque audio');
        $product3->setDescription('Casque circum-aural avec réduction de bruit');
        $product3->setPrice('129.90');
        $product3->setType('physique');
        $product3->setWeight(0.3);
        $product3->setHeight(18.0);
        $product3->setWidth(16.0);
        $manager->persist($product3);

        $manager->flush();
    }
}
