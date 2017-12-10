<?php
namespace src\AppBundle\Form;

use AppBundle\Form\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testSubmitValidData()
    {
        $formData = array(
            'username' => 'user',
            'password' => 'pass',
        );

        $form = $this->factory->create(LoginType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
