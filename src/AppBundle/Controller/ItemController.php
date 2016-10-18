<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Item;
use AppBundle\Form\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends Controller
{
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
                $errors = $form->getErrors();
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
     * @return ItemRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Item');
    }
}
