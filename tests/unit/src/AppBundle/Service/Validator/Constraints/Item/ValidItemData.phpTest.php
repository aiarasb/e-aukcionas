<?php
namespace src\AppBundle\Service\Validator\Constraints\Item;


use AppBundle\Service\Validator\Constraints\Item\ValidItemData;

class ValidItemDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function test()
    {
        $constraint = new ValidItemData();
        $this->assertSame('class', $constraint->getTargets());
    }
}
