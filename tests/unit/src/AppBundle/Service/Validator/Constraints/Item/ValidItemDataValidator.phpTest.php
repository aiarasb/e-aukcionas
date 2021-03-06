<?php

namespace src\AppBundle\Service\Validator\Constraints\Item;

use AppBundle\Entity\Item;
use AppBundle\Service\Validator\Constraints\Item\ValidItemData;
use AppBundle\Service\Validator\Constraints\Item\ValidItemDataValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class ValidItemDataValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @return array
     */
    public function getTestValidateData()
    {
        $cases = [];

        //case #0
        $item = new Item();
        $item->setBuyNowPrice(1);
        $cases[] = [
            $item,
            null,
            0,
        ];

        //case 1
        $item = new Item();
        $cases[] = [
            $item,
            '"Pradinė kaina" arba "Pirk dabar kaina" turi būti įvesta.',
            1,
        ];

        //case #2
        $item = new Item();
        $item->setBasePrice(1);
        $item->setAuctionStart(new \DateTime());
        $item->setAuctionEnd(new \DateTime());
        $cases[] = [
            $item,
            null,
            0,
        ];

        //case #3
        $item = new Item();
        $item->setBuyNowPrice(1);
        $item->setAuctionStart(new \DateTime());
        $item->setAuctionEnd(new \DateTime());
        $cases[] = [
            $item,
            'Norint skelbti aukcioną privaloma nurodyti pradinę kainą, aukciono pradžią bei pabaigą.',
            1,
        ];

        //case #4
        $item = new Item();
        $item->setBasePrice(1);
        $item->setAuctionEnd(new \DateTime());
        $cases[] = [
            $item,
            'Norint skelbti aukcioną privaloma nurodyti pradinę kainą, aukciono pradžią bei pabaigą.',
            1,
        ];

        //case #5
        $item = new Item();
        $item->setBasePrice(1);
        $item->setAuctionStart(new \DateTime());
        $cases[] = [
            $item,
            'Norint skelbti aukcioną privaloma nurodyti pradinę kainą, aukciono pradžią bei pabaigą.',
            1,
        ];

        return $cases;
    }

    /**
     * @dataProvider getTestValidateData
     * @param Item   $value
     * @param string $expected
     * @param int    $count
     */
    public function testValidate($value, $expected, $count)
    {
        $validator = $this->getValidator();
        $constraint = $this->getConstraint();
        $context = $this->getContext($count, $expected);

        $validator->initialize($context);
        $validator->validate($value, $constraint);
    }

    /**
     * @param int         $count
     * @param string|null $expected
     * @return ExecutionContext
     */
    private function getContext($count, $expected)
    {
        $context = $this->getMockBuilder(ExecutionContext::class)
                        ->disableOriginalConstructor()->setMethods(['buildViolation'])->getMock();
        $context->expects($this->exactly($count))->method('buildViolation')
                ->with($this->equalTo($expected))
                ->willReturn($this->getMockConstraintViolationBuilder());

        /** @var ExecutionContext $context */
        return $context;
    }

    /**
     * @return ConstraintViolationBuilder
     */
    private function getMockConstraintViolationBuilder()
    {
        $builder = $this->getMockBuilder(ConstraintViolationBuilder::class)
                        ->disableOriginalConstructor()->getMock();
        $builder->method('setCode')->willReturn($builder);
        $builder->method('addViolation');

        /** @var ConstraintViolationBuilder $builder */
        return $builder;
    }

    /**
     * @return ValidItemData
     */
    private function getConstraint()
    {
        $constraint = new ValidItemData();

        return $constraint;
    }

    /**
     * @return ValidItemDataValidator
     */
    private function getValidator()
    {
        $validator = new ValidItemDataValidator();

        return $validator;
    }
}
