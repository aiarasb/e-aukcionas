<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\BidRepository;
use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Doctrine\Repository\UserRepository;
use AppBundle\Entity\Item;
use AppBundle\Entity\User;
use AppBundle\Form\EmailChangeType;
use AppBundle\Form\LoginType;
use AppBundle\Form\PasswordChangeType;
use AppBundle\Form\RegisterType;
use AppBundle\Service\User\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $loginForm = $this->createForm(LoginType::class);
        $loginForm->handleRequest($request);
        $error = false;

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $loginData = $loginForm->getData();
            $success = $this->get('user_manager')->logIn($loginData['username'], $loginData['password']);

            if ($success) {
                return $this->redirectToRoute('index');
            } else {
                $error = true;
            }
        }

        return $this->render(
            'AppBundle:user:login.html.twig',
            [
                'loginForm' => $loginForm->createView(),
                'error' => $error
            ]
        );
    }

    /**
     * @return RedirectResponse
     */
    public function logoutAction()
    {
        $this->get('user_manager')->logOut();
        return $this->redirectToRoute('index');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $registerForm = $this->createForm(
            RegisterType::class,
            $user
        );

        $registerForm->handleRequest($request);
        $errors = null;

        if ($registerForm->isSubmitted()) {
            if ($registerForm->isValid()) {
                $this->getRepository()->register($user);
                return $this->redirectToRoute('login');
            } else {
                $errors = $registerForm->getErrors();
            }
        }

        return $this->render(
            'AppBundle:user:register.html.twig',
            [
                'registerForm' => $registerForm->createView(),
                'errors' => $errors
            ]
        );
    }

    public function itemsAction()
    {
        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getDoctrine()->getRepository('AppBundle:Item');
        /** @var UserManager $userManager */
        $userManager = $this->get('user_manager');

        $items = $itemRepository->findBy(['owner' => $userManager->getUser()]);
        $items = $this->resolveStatus($items);

        return $this->render(
            'AppBundle:user:items.html.twig',
            [
                'items' => $items
            ]
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function settingsAction(Request $request)
    {
        $user = $this->get('user_manager')->getUser();
        if (null === $user) {
            return $this->redirect('login');
        }

        $passwordForm = $this->createForm(PasswordChangeType::class);
        $emailForm = $this->createForm(EmailChangeType::class);

        if ('POST' === $request->getMethod()) {
            if ($request->request->has($passwordForm->getName())) {
                $passwordForm->handleRequest($request);
                if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                    //TODO: change pass
                }
            } elseif ($request->request->has($emailForm->getName())) {
                $emailForm->handleRequest($request);
                if ($emailForm->isSubmitted() && $emailForm->isValid()) {
                    //TODO: change email
                }
            }
        }

        return $this->render(
            'AppBundle:user:settings.html.twig',
            [
                'user' => $user,
                'passwordForm' => $passwordForm->createView(),
                'emailForm' => $emailForm->createView()
            ]
        );
    }

    private function resolveStatus($items)
    {
        $now = new \DateTime();
        /**
         * @var Item $item
         */
        foreach ($items as $key => $item) {
            if ($item->getAuctionEnd() !== null && $item->getAuctionEnd()->getTimestamp() < $now->getTimestamp()) {
                if ($item->getCurrentPrice() > 0) {
                    $item->setStatus(ItemRepository::STATUS_SOLD);
                    /** @var BidRepository $bidRepository */
                    $bidRepository = $this->getDoctrine()->getRepository('AppBundle:Bid');
                    $buyer = $bidRepository->getHighestBidder($item);
                    $item->setBuyer($buyer);
                } else {
                    $item->setStatus(ItemRepository::STATUS_FINISHED);
                }
            }
            $items[$key] = $item;
        }

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getDoctrine()->getRepository('AppBundle:Item');
        $itemRepository->updateArray($items);

        return $items;
    }

    /**
     * @return UserRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:User');
    }
}
