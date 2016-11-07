<?php

namespace AppBundle\Service\Validator\Constraints\Item;

use Symfony\Component\Validator\Constraint;

class ValidItemData extends Constraint
{
    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
