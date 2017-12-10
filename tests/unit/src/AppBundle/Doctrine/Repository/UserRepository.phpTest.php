<?php

namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\UserRepository;
use AppBundle\Entity\User;

class UserRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testRegister()
    {
        /** @var UserRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(User::class);

        $user = new User();
        $user->setUsername('b');
        $user->setEmail('b@b.b');
        $user->setPassword('b');
        $user->setFirstName('B');
        $user->setLastName('B');

        $repository->register($user);
        $this->tester->seeInRepository(
            User::class,
            [
                'username'  => 'b',
                'email'     => 'b@b.b',
                'firstName' => 'B',
                'lastName'  => 'B',
            ]
        );
    }
}
