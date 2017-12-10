<?php

namespace src\AppBundle\Service\Validator\Constraints\User;


use AppBundle\Entity\User;
use AppBundle\Service\Validator\Constraints\User\UniqueUserData;
use AppBundle\Service\Validator\Constraints\User\UniqueUserDataValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class UniqueUserDataValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {
        $this->tester->haveInRepository(
            'AppBundle\Entity\User',
            [
                'username'    => 'b',
                'email'       => 'b@b.b',
                'password'    => 'ss',
                'firstName'   => 'B',
                'lastName'    => 'B',
                'created'     => new \DateTime(),
                'lastUpdated' => new \DateTime(),
                'role'        => 'user',
            ]
        );
    }

    /**
     * @return array
     */
    public function getTestValidateData()
    {
        $cases = [];

        //case #0
        $user = new User();
        $user->setUsername('a');
        $user->setEmail('a@a.a');
        $cases[] = [
            $user,
            0,
        ];

        //case #1
        $user = new User();
        $user->setUsername('b');
        $user->setEmail('a@a.a');
        $cases[] = [
            $user,
            1,
        ];

        //case #2
        $user = new User();
        $user->setUsername('a');
        $user->setEmail('b@b.b');
        $cases[] = [
            $user,
            1,
        ];

        //case #3
        $user = new User();
        $user->setUsername('b');
        $user->setEmail('b@b.b');
        $cases[] = [
            $user,
            2,
        ];

        return $cases;
    }

    /**
     * @dataProvider getTestValidateData
     * @param User $value
     * @param int  $count
     */
    public function testValidate($value, $count)
    {
        $validator = $this->getValidator();
        $constraint = $this->getConstraint();
        $context = $this->getContext($count);

        $validator->initialize($context);
        $validator->validate($value, $constraint);
    }

    /**
     * @param int $count
     * @return ExecutionContext
     */
    private function getContext($count)
    {
        $context = $this->getMockBuilder(ExecutionContext::class)
                        ->disableOriginalConstructor()->setMethods(['buildViolation'])->getMock();
        $context->expects($this->exactly($count))->method('buildViolation')
                ->with($this->equalTo('Toks %field% jau egzistuoja.'))
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
        $builder->method('setParameter')->willReturn($builder);
        $builder->method('addViolation');

        /** @var ConstraintViolationBuilder $builder */
        return $builder;
    }

    /**
     * @return UniqueUserData
     */
    private function getConstraint()
    {
        $constraint = new UniqueUserData();

        return $constraint;
    }

    /**
     * @return UniqueUserDataValidator
     */
    private function getValidator()
    {
        $validator = new UniqueUserDataValidator($this->getModule('Doctrine2')->em);

        return $validator;
    }
}
