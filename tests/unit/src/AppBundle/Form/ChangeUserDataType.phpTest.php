<?php
namespace src\AppBundle\Form;


use AppBundle\Form\ChangeUserDataType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class ChangeUserDataTypeTest extends TypeTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testSubmitValidData()
    {
        $formData = [
            'firstName' => 'Aa',
            'lastName'  => 'Bb',
            'address'   => 'AB st. 11 a',
            'phone'     => '12345',
            'city'      => 'AAA',
        ];

        $form = $this->factory->create(ChangeUserDataType::class);

        $object = new User();
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
