<?php

namespace src\AppBundle\Form;


use AppBundle\Entity\Rating;
use AppBundle\Form\RatingType;
use Symfony\Component\Form\Test\TypeTestCase;

class RatingTypeTest extends TypeTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testSubmitValidData()
    {
        $formData = [
            'comment' => 'aaa',
            'rate'    => 5,
        ];

        $form = $this->factory->create(RatingType::class);

        $object = new Rating();
        $object->setComment('aaa');
        $object->setRate(5);

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
