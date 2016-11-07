<?php

namespace AppBundle\Service\Validator\Constraints\User;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ContainsPartnerDataValidator
 */
class UniqueUserDataValidator extends ConstraintValidator
{
    /** @var  ManagerRegistry */
    protected $managerRegistry;

    /** @var  Constraint */
    private $constraint;

    /**
     * ContainsPartnerDataValidator constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param User $value
     * @param Constraint      $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $this->constraint = $constraint;

        $this->validateUsername($value->getUsername());
        $this->validateEmail($value->getEmail());
    }

    private function validateUsername($username)
    {
        $duplicate = $this->managerRegistry->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

        if (null !== $duplicate) {
            $this->addViolation('vartotojo vardas');
        }
    }

    private function validateEmail($email)
    {
        $duplicate = $this->managerRegistry->getRepository('AppBundle:User')->findOneBy(['email' => $email]);

        if (null !== $duplicate) {
            $this->addViolation('el. pašto adresas');
        }
    }

    /**
     * @param string $field
     */
    private function addViolation($field)
    {
        $this->context->buildViolation($this->constraint->message)
            ->setCode('error')
            ->setParameter('%field%', $field)
            ->addViolation();
    }
}
