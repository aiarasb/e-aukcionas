<?php

namespace src\AppBundle\Form;


use AppBundle\Entity\User;
use AppBundle\Form\RegisterType;
use Symfony\Component\Form\Test\TypeTestCase;

class RegisterTypeTest extends TypeTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testSubmitValidData()
    {
        $formData = [
            'username'  => 'user',
            'password'  => 'pass',
            'email'     => 'e@mai.l',
            'firstName' => 'Aa',
            'lastName'  => 'Bb',
            'address'   => 'AB st. 11 a',
            'phone'     => '12345',
            'city'      => 'AAA',
        ];

        $form = $this->factory->create(RegisterType::class);

        $object = new User();
        $object->setUsername('user');
        $object->setPassword('pass');
        $object->setEmail('e@mai.l');
        $object->setFirstName('Aa');
        $object->setLastName('Bb');
        $object->setAddress('AB st. 11 a');
        $object->setPhone('12345');
        $object->setCity('AAA');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
