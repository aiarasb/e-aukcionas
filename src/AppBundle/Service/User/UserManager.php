<?php

namespace AppBundle\Service\User;

use AppBundle\Doctrine\Repository\UserRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class UserManager {
    /** @var  EntityManager */
    private $entityManager;

    /** @var  Session */
    private $session;

    /**
     * UserManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager, $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function logIn($username, $password)
    {
        $success = false;
        $exists = $this->getRepository()->exists($username, $password);

        if ($exists) {
            $this->session->set('username', $username);
            $this->session->set('loginToken', md5($username.md5($password)));
            $success = true;
        }

        return $success;
    }

    /**
     * Logs out
     */
    public function logOut()
    {
        $this->session->remove('username');
        $this->session->remove('loginToken');
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        $user = null;
        if ($this->isLoggedIn()) {
            $username = $this->session->get('username');
            /** @var User $user */
            $user = $this->getRepository()->findOneBy(['username' => $username]);
        }

        return $user;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        $username = $this->session->get('username');
        $loginToken = $this->session->get('loginToken');
        return $this->getRepository()->isLoggedIn($username, $loginToken);
    }

    /**
     * @return UserRepository
     */
    private function getRepository()
    {
        return $this->entityManager->getRepository('AppBundle:User');
    }
}
