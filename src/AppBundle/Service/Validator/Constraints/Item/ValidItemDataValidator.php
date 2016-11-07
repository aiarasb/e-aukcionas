<?php

namespace AppBundle\Service\Validator\Constraints\Item;

use AppBundle\Entity\Item;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ContainsPartnerDataValidator
 */
class ValidItemDataValidator extends ConstraintValidator
{
    /** @var  Constraint */
    private $constraint;

    /**
     * @param Item $value
     * @param Constraint      $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $this->constraint = $constraint;

        if (0 == $value->getBasePrice() && 0 == $value->getBuyNowPrice()) {
            $this->addViolation('"Pradinė kaina" arba "Pirk dabar kaina" turi būti įvesta.');
        }

        if (($value->getBasePrice() == 0 || $value->getAuctionStart() === null || $value->getAuctionEnd() === null)
            && ($value->getBasePrice() > 0 || $value->getAuctionStart() !== null || $value->getAuctionEnd() !== null)) {
            $this->addViolation('Norint skelbti aukcioną privaloma nurodyti pradinę kainą, aukciono pradžią bei pabaigą.');
        }
    }

    /**
     * @param string $message
     */
    private function addViolation($message)
    {
        $this->context->buildViolation($message)
            ->setCode('error')
            ->addViolation();
    }
}
