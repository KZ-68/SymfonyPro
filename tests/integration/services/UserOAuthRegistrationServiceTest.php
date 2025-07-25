<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserOAuthRegistrationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class UserOAuthRegistrationServiceTest extends TestCase
{
    public function testCreateUserValidData(): void
    {
        $user = new User();

        $userResponse = $this->createMock(UserResponseInterface::class);
        $userResponse->method('getEmail')->willReturn('user@example.com');
        $userResponse->method('getNickname')->willReturn('Albert8451');
        $userResponse->method('getFirstName')->willReturn('Albert');
        $userResponse->method('getLastName')->willReturn('Tempé');

        $form = $this->createMock(FormInterface::class);
        $agreeTermsForm = $this->createMock(FormInterface::class);
        $agreeTermsForm->method('getData')->willReturn(true);

        $form->expects($this->once())
             ->method('setData')
             ->with($this->callback(function ($data) use (&$user) {
                 return $data instanceof User;
             }));

        $form->method('handleRequest')->willReturnSelf();
        $form->method('get')->with('agreeTerms')->willReturn($agreeTermsForm);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $request = new Request();

        $service = new UserOAuthRegistrationService();
        $result = $service->createUserFromForm($form, $user, $userResponse, $request);

        $this->assertTrue($result);
        $this->assertSame('user@example.com', $user->getEmail());
        $this->assertSame('Albert8451', $user->getUsername());
        $this->assertSame('Albert', $user->getFirstName());
        $this->assertSame('Tempé', $user->getLastName());
        $this->assertTrue($user->hasAgreedTerms());
        $this->assertFalse($user->isVerified());
    }

    public function testCreateUserInvalidForm(): void
    {
        $user = new User();

        $userResponse = $this->createMock(UserResponseInterface::class);
        $userResponse->method('getEmail')->willReturn('user@example.com');
        $userResponse->method('getNickname')->willReturn('Albert8451');
        $userResponse->method('getFirstName')->willReturn('Albert');
        $userResponse->method('getLastName')->willReturn('Tempé');

        $form = $this->createMock(FormInterface::class);
        $agreeTermsForm = $this->createMock(FormInterface::class);
        $agreeTermsForm->method('getData')->willReturn(false);

        $form->method('handleRequest')->willReturnSelf();
        $form->method('get')->with('agreeTerms')->willReturn($agreeTermsForm);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);

        $request = new Request();

        $service = new UserOAuthRegistrationService();
        $result = $service->createUserFromForm($form, $user, $userResponse, $request);

        $this->assertFalse($result);
        $this->assertFalse($user->hasAgreedTerms());
    }
}