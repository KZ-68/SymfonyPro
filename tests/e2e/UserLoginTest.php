<?php

namespace App\Tests\E2e;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class UserLoginTest extends PantherTestCase
{
    public function test(): void
    {
        $client = Client::createChromeClient(__DIR__.'/../../drivers/chromedriver',);
        $client->request('GET', '/home');

        $this->assertPageTitleContains('Symfony Professional Panel');

        $this->assertSelectorIsEnabled('.login-link');
        $this->assertSelectorIsEnabled('.login-register');

        $client->request('GET', '/login');

        $this->assertSelectorExists('[type="submit"]');

        $client->submitForm('Sign in', ['email' => 'test@exemple.com', 'password' => '{OO@a2b6}!#_?']);
    }
}