<?php

namespace App\Tests\E2e;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class UserLoginTest extends PantherTestCase

{

    public function test(): void
    {
        // $client = Client::createChromeClient(__DIR__.'/../../drivers/chromedriver.exe', null, [], 'http://127.0.0.1:8000');
        $client = static::createPantherClient(
        options: [
            'browser' => PantherTestCase::CHROME,
            'enable_output' => true
        ],
        kernelOptions: [
            
        ],
        managerOptions: [
        ]
        );
        $client->request('GET', '/home');
        
        $this->assertPageTitleContains('Home');

        $this->assertSelectorIsEnabled('.login-link');
        $this->assertSelectorIsEnabled('.register-link');

        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('[type="submit"]');

        $client->submitForm('Sign in', ['_username' => 'test@exemple.com', '_password' => '{OO@a2b6}!#_?']);

        $client->waitFor('#profile-wrapper');

        $this->assertPageTitleContains('Profile');
    }
}