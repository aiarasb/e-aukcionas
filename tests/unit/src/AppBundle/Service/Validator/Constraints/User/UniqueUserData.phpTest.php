<?php
namespace src\AppBundle\Service\Validator\Constraints\User;


use AppBundle\Service\Validator\Constraints\User\UniqueUserData;

class UniqueUserDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function test()
    {
        $constraint = new UniqueUserData();
        $this->assertSame('class', $constraint->getTargets());
        $this->assertSame('Toks %field% jau egzistuoja.', $constraint->message);
    }
}
