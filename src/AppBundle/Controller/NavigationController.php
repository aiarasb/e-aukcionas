<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NavigationController extends Controller
{
    /**
     * @return Response
     */
    public function userMenuAction()
    {
        return $this->render(
            'AppBundle:navbar:userNavBar.html.twig',
            [
                'user' => $this->get('user_manager')->getUser(),
                'loggedIn' => $this->get('user_manager')->isLoggedIn()
            ]
        );
    }
}
