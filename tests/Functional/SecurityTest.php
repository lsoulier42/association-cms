<?php

namespace App\Tests\Functional;

class SecurityTest extends AbstractFunctionalTestCase
{
    public function testLoginPageIsPublic(): void
    {
        $client = $this->createClientWithDatabase();

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        self::assertSelectorExists('input[name="email"]');
        self::assertSelectorExists('input[name="password"]');
    }

    public function testAdminRequiresAuthentication(): void
    {
        $client = $this->createClientWithDatabase();

        $client->request('GET', '/admin');

        self::assertResponseRedirects('/login');
    }

    public function testLoginWithValidCredentialsGivesAccessToAdmin(): void
    {
        $client = $this->createClientWithDatabase();

        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);

        self::assertResponseRedirects('/admin');

        $client->request('GET', '/admin');
        self::assertResponseIsSuccessful();
    }
}
