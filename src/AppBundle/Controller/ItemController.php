<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Item;
use AppBundle\Entity\User;
use AppBundle\Form\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(
            ItemType::class,
            $item
        );

        $form->handleRequest($request);
        $errors = null;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $item->setOwner($this->get('user_manager')->getUser());
                $this->getRepository()->create($item);
                return $this->redirectToRoute('upload_photo', ['itemId' => $item->getId()]);
            } else {
                $errors = $form->getErrors(true, true);
            }
        }

        return $this->render(
            'AppBundle:item:new.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }

    /**
     * @param int $itemId
     * @return RedirectResponse|Response
     */
    public function buyAction($itemId)
    {
        /** @var Item $item */
        $item = $this->getRepository()->find($itemId);
        /** @var User $buyer */
        $buyer = $this->get('user_manager')->getUser();

        if (null === $item || null === $buyer) {
            return $this->redirectToRoute('index');
        }

        $item->setStatus('sold');
        $item->setBuyer($buyer);
        $this->getRepository()->update($item);

        return $this->render(
            'AppBundle:item:sold.html.twig',
            [
                'item' => $item
            ]
        );
    }

    /**
     * @return ItemRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Item');
    }
}
