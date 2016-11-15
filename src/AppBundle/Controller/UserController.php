<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\BidRepository;
use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Doctrine\Repository\RatingRepository;
use AppBundle\Doctrine\Repository\UserRepository;
use AppBundle\Entity\Item;
use AppBundle\Entity\Rating;
use AppBundle\Entity\User;
use AppBundle\Form\ChangeUserDataType;
use AppBundle\Form\EmailChangeType;
use AppBundle\Form\LoginType;
use AppBundle\Form\PasswordChangeType;
use AppBundle\Form\RatingType;
use AppBundle\Form\RegisterType;
use AppBundle\Service\User\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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
        $message = null;
        /** @var UserManager $userManager */
        $userManager = $this->get('user_manager');
        if (null === $userManager->getUser()) {
            return $this->redirect('login');
        }

        $passwordForm = $this->createForm(PasswordChangeType::class);
        $emailForm = $this->createForm(EmailChangeType::class);
        $changeDataForm = $this->createForm(ChangeUserDataType::class, $userManager->getUser());

        if (count($request->query)) {
            $message = $this->resolveSettingsQuery($request);
        } elseif ('POST' === $request->getMethod()) {
            $message = $this->resolveSettingsPost($request, $passwordForm, $emailForm, $changeDataForm);
        }

        return $this->render(
            'AppBundle:user:settings.html.twig',
            [
                'user' => $userManager->getUser(),
                'message' => $message,
                'passwordForm' => $passwordForm->createView(),
                'emailForm' => $emailForm->createView(),
                'changeDataForm' => $changeDataForm->createView(),
                'items' => $this->getDoctrine()->getRepository('AppBundle:Item')->findAll(),
                'newItems' => $this->getDoctrine()->getRepository('AppBundle:Item')->getNew(),
                'users' => $this->getDoctrine()->getRepository('AppBundle:User')->findAll()
            ]
        );
    }

    /**
     * @param Request $request
     * @param string  $username
     * @return Response
     */
    public function showUserAction(Request $request, $username)
    {
        $user = $this->getRepository()->findOneBy(['username'=>$username]);
        $rating = new Rating();
        $ratingForm = $this->createForm(RatingType::class, $rating);
        $ratingForm->handleRequest($request);

        if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
            $rating->setAuthor($this->get('user_manager')->getUser());
            $rating->setReceiver($user);
            /** @var RatingRepository $ratingRepository */
            $ratingRepository = $this->getDoctrine()->getRepository('AppBundle:Rating');
            $ratingRepository->create($rating);
        }


        return $this->render(
            'AppBundle:user:show.html.twig',
            [
                'user' => $user,
                'ratingForm' => $ratingForm->createView()
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

    /**
     * @param Request $request
     * @param Form $passwordForm
     * @param Form $emailForm
     * @param Form $changeDataForm
     * @return string|null
     */
    private function resolveSettingsPost(Request $request, $passwordForm, $emailForm, $changeDataForm)
    {
        /** @var UserManager $userManager */
        $userManager = $this->get('user_manager');
        $message = null;

        if ($request->request->has($passwordForm->getName())) {
            $passwordForm->handleRequest($request);
            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                $data = $passwordForm->getData();
                $message = $userManager->changePassword($data['oldPassword'], $data['newPassword']);
            }
        } elseif ($request->request->has($emailForm->getName())) {
            $emailForm->handleRequest($request);
            if ($emailForm->isSubmitted() && $emailForm->isValid()) {
                $data = $emailForm->getData();
                $message = $userManager->changeEmail($data['email']);
            }
        } elseif ($request->request->has($changeDataForm->getName())) {
            $changeDataForm->handleRequest($request);
            if ($changeDataForm->isSubmitted() && $changeDataForm->isValid()) {
                $message = $userManager->updateUser();
            }
        }
        return $message;
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function resolveSettingsQuery(Request $request)
    {
        /** @var UserManager $userManager */
        $userManager = $this->get('user_manager');
        $action = $request->query->get('action');
        $message = null;

        if ('deactivateUser' == $action && $userManager->getUser()->getRole() == UserRepository::ROLE_ADMIN) {
            /** @var User $user */
            $user = $this->getRepository()->find($request->query->get('userId'));
            if (null !== $user) {
                $user->setActive(false);
                $this->getRepository()->update($user);
                $message = 'Vartotojas '.$user->getUsername().' deaktyvuotas.';
            }
        } elseif ('activateUser' == $action && $userManager->getUser()->getRole() == UserRepository::ROLE_ADMIN) {
            /** @var User $user */
            $user = $this->getRepository()->find($request->query->get('userId'));
            if (null !== $user) {
                $user->setActive(true);
                $this->getRepository()->update($user);
                $message = 'Vartotojas '.$user->getUsername().' aktyvuotas.';
            }
        } elseif ('setUserModerator' == $action && $userManager->getUser()->getRole() == UserRepository::ROLE_ADMIN) {
            /** @var User $user */
            $user = $this->getRepository()->find($request->query->get('userId'));
            if (null !== $user) {
                $user->setRole(UserRepository::ROLE_MODERATOR);
                $this->getRepository()->update($user);
                $message = 'Vartotojui '.$user->getUsername().' suteiktos moderatoriaus teisės.';
            }
        } elseif ('unsetUserModerator' == $action && $userManager->getUser()->getRole() == UserRepository::ROLE_ADMIN) {
            /** @var User $user */
            $user = $this->getRepository()->find($request->query->get('userId'));
            if (null !== $user) {
                $user->setRole(UserRepository::ROLE_USER);
                $this->getRepository()->update($user);
                $message = 'Vartotojui '.$user->getUsername().' atimtos moderatoriaus teisės.';
            }
        } elseif ('confirmItem' == $action && $userManager->getUser()->getRole() >= UserRepository::ROLE_MODERATOR) {
            /** @var ItemRepository $itemRepository */
            $itemRepository = $this->getDoctrine()->getRepository('AppBundle:Item');
            /** @var Item $item */
            $item = $itemRepository->find($request->query->get('itemId'));
            if (null !== $item) {
                $item->setStatus(ItemRepository::STATUS_SELLING);
                $itemRepository->update($item);
                $message = 'Prekė patvirtinta.';
            }
        } elseif ('blockItem' == $action && $userManager->getUser()->getRole() >= UserRepository::ROLE_MODERATOR) {
            /** @var ItemRepository $itemRepository */
            $itemRepository = $this->getDoctrine()->getRepository('AppBundle:Item');
            /** @var Item $item */
            $item = $itemRepository->find($request->query->get('itemId'));
            if (null !== $item) {
                $item->setStatus(ItemRepository::STATUS_BLOCKED);
                $itemRepository->update($item);
                $message = 'Prekė užblokuota.';
            }
        }

        return $message;
    }
}
