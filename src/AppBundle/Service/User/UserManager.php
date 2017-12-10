<?php

namespace AppBundle\Service\User;

use AppBundle\Doctrine\Repository\UserRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class UserManager
{
    /** @var  EntityManager */
    private $entityManager;

    /** @var  Session */
    private $session;

    /** @var  User */
    private $user = null;

    /** @var  bool */
    private $loggedIn = null;

    /**
     * UserManager constructor.
     * @param EntityManager $entityManager
     * @param Session       $session
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
            $this->session->set('loginToken', md5($username . md5($password)));
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
        if (null === $this->loggedIn && null === $this->user) {
            $this->resolveUser();
        }

        return $this->user;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        if (null === $this->loggedIn) {
            $this->resolveUser();
        }

        return $this->loggedIn;
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @return string
     */
    public function changePassword($oldPassword, $newPassword)
    {
        $message = 'Neteisingas senas slaptažodis.';
        $user = $this->getUser();
        if (md5($oldPassword) == $user->getPassword()) {
            $user->setPassword(md5($newPassword));
            $this->getRepository()->update($user);
            $message = 'Slaptažodis sėkmingai pakeistas.';
        }

        return $message;
    }

    public function changeEmail($email)
    {
        $message = 'Naujas el. paštas turi skirtis nuo seno.';
        $user = $this->getUser();

        if ($user->getEmail() != $email) {
            $user->setEmail($email);
            $this->getRepository()->update($user);
            $message = 'El. paštas sėkmingai pakeistas.';
        }

        return $message;
    }

    public function updateUser()
    {
        $message = 'Duomenys sėkmingai atnaujinti.';
        $this->getRepository()->update($this->getUser());

        return $message;
    }

    /**
     * @return UserRepository
     */
    private function getRepository()
    {
        return $this->entityManager->getRepository('AppBundle:User');
    }

    /**
     * Resolve user
     */
    private function resolveUser()
    {
        $username = $this->session->get('username');
        $loginToken = $this->session->get('loginToken');
        /** @var User $user */
        $this->user = $this->getRepository()->isLoggedIn($username, $loginToken);
        if ($this->user === null) {
            $this->loggedIn = false;
        } else {
            $this->loggedIn = true;
        }
    }
}
