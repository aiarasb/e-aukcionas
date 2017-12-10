<?php

namespace src\AppBundle\Service\User;


use AppBundle\Service\User\UserManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class UserManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveInRepository(
            'AppBundle\Entity\User',
            [
                'username'    => 'b',
                'email'       => 'b@b.b',
                'password'    => md5('b'),
                'firstName'   => 'B',
                'lastName'    => 'B',
                'created'     => new \DateTime(),
                'lastUpdated' => new \DateTime(),
                'role'        => 'user',
                'active'      => true,
            ]
        );
    }

    // tests
    public function test()
    {
        $session = new Session(new MockArraySessionStorage());
        $manager = $this->getManager($session);

        $this->assertNull($manager->getUser());
        $this->assertFalse($manager->isLoggedIn());
        $this->assertFalse($manager->logIn('a', 'a'));
        $this->assertNull($manager->getUser());
        $this->assertFalse($manager->isLoggedIn());

        $manager = $this->getManager($session);
        $this->assertTrue($manager->logIn('b', 'b'));
        $this->assertTrue($manager->isLoggedIn());
        $this->assertNotNull($manager->getUser());
        $this->assertSame(
            "Neteisingas senas slaptažodis.",
            $manager->changePassword('a', 'a')
        );
        $this->assertSame(
            "Slaptažodis sėkmingai pakeistas.",
            $manager->changePassword('b', 'a')
        );
        $this->assertSame(
            "Naujas el. paštas turi skirtis nuo seno.",
            $manager->changeEmail('b@b.b')
        );
        $this->assertSame(
            "El. paštas sėkmingai pakeistas.",
            $manager->changeEmail('a@a.a')
        );
        $this->assertSame(
            "Duomenys sėkmingai atnaujinti.",
            $manager->updateUser()
        );

        $manager = $this->getManager($session);
        $manager->logOut();
        $this->assertNull($manager->getUser());
        $this->assertFalse($manager->isLoggedIn());
    }

    /**
     * @param $session
     * @return UserManager
     */
    private function getManager($session)
    {
        $manager = new UserManager(
            $this->getModule('Doctrine2')->em,
            $session
        );

        return $manager;
    }
}
