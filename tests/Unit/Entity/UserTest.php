<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('testmail@exemple.com');

        $this->assertSame('testmail@exemple.com', $user->getEmail(), 'L\'email ne correspond pas à la valeur attendue');
    }

    public function testName(): void
    {
        $user = new User();
        $user->setName('testname');

        $this->assertSame('testname', $user->getName(), 'Le nom ne correspond pas à la valeur attendue');
    }

    public function testDescription(): void
    {
        $user = new User();
        $user->setDescription('testdescription');

        $this->assertSame('testdescription', $user->getDescription(), 'La description ne correspond pas à la valeur attendue');
    }

    public function testMedias(): void
    {
        $user = new User();

        $this->assertCount(0, $user->getMedias(), 'La collection de médias doit être initialisée');
    }

    public function testDefaultRole(): void
    {
        $user = new User();

        $this->assertEqualsCanonicalizing(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles(), 'Le rôle ne correspond pas à la valeur attendue par défaut');
    }

    public function testBasicRole(): void
    {
        $user = new User();
        $user->setAdmin(false);
        $user->setSuperAdmin(false);

        $this->assertEqualsCanonicalizing(['ROLE_USER'], $user->getRoles(), 'Le rôle ne correspond pas à la valeur attendue par défaut');
    }

    public function testAdminRole(): void
    {
        $user = new User();
        $user->setAdmin(true);
        $user->setSuperAdmin(false);

        $this->assertEqualsCanonicalizing(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles(), 'Le rôle ne correspond pas à la valeur attendue par défaut');
    }

    public function testSuperAdminRole(): void
    {
        $user = new User();
        $user->setAdmin(true);
        $user->setSuperAdmin(true);

        $this->assertEqualsCanonicalizing(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], $user->getRoles(), 'Le rôle ne correspond pas à la valeur attendue par défaut');
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('testpassword');

        $this->assertSame('testpassword', $user->getPassword(), 'Le mot de passe ne correspond pas à la valeur attendue');
    }

    public function testDates(): void
    {
        $user = new User();
        $createdAt = new \DateTimeImmutable('2026-01-01 16:00:00');
        $updatedAt = new \DateTimeImmutable('2026-01-02 16:30:00');
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);

        $this->assertEquals($createdAt, $user->getCreatedAt(), 'La date de création ne correspond pas à la valeur attendue');
        $this->assertEquals($updatedAt, $user->getUpdatedAt(), 'La date de mise à jour ne correspond pas à la valeur attendue');
    }

    public function testPrePersist(): void
    {
        $user = new User();
        $user->prePersist();

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt(), 'La date de création n\'est pas une instance de DateTimeImmutable');
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt(), 'La date de mise à jour n\'est pas une instance de DateTimeImmutable');
        $this->assertEquals($user->getCreatedAt(), $user->getUpdatedAt(), 'La date de création et la date de mise à jour ne sont pas identiques après la création');
    }

    public function testPreUpdate(): void
    {
        $user = new User();
        $user->prePersist();

        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();
        $user->preUpdate();

        $this->assertEquals($createdAt, $user->getCreatedAt(), 'La date de création a été modifiée lors de la mise à jour');
        $this->assertNotEquals($updatedAt, $user->getUpdatedAt(), 'La date de mise à jour n\'a pas été modifiée lors de la mise à jour');
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('testmail@exemple.com');

        $this->assertSame('testmail@exemple.com', $user->getUserIdentifier(), 'L\'identifiant ne correspond pas à la valeur attendue');
    }
}
