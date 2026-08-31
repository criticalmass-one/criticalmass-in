<?php declare(strict_types=1);

namespace Tests\Form;

use App\Form\Type\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    public function testRememberMeIsCheckedByDefault(): void
    {
        $form = $this->factory->create(LoginType::class);

        $this->assertTrue($form->get('remember_me')->getData());
    }

    public function testRememberMeStaysUncheckedWhenNotSubmitted(): void
    {
        $form = $this->factory->create(LoginType::class);

        $form->submit(['email' => 'cyclist@example.org']);

        $this->assertFalse($form->getData()['remember_me']);
    }

    public function testRememberMeIsSubmitted(): void
    {
        $form = $this->factory->create(LoginType::class);

        $form->submit(['email' => 'cyclist@example.org', 'remember_me' => '1']);

        $this->assertTrue($form->getData()['remember_me']);
    }
}
