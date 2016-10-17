<?php

namespace AppBundle\Service\Validator\Constraints\User;

use Symfony\Component\Validator\Constraint;

class UniqueUserData extends Constraint
{
    /** @var string */
    public $message = 'This "%field%" already exists.';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
