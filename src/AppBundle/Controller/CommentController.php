<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\CommentRepository;
use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Item;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @param int     $id
     * @return RedirectResponse
     */
    public function commentAction(Request $request, $id)
    {
        $comment = new Comment();
        $form = $this->createForm(
            CommentType::class,
            $comment
        );
        /** @var Item $item */
        $item = $this->getItemRepository()->find($id);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment->setAuthor($this->get('user_manager')->getUser());
                $comment->setItem($item);
                $this->getRepository()->create($comment);
            }
        }

        return $this->redirectToRoute('auction', ['id' => $item->getId()]);
    }

    /**
     * @return CommentRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Comment');
    }

    /**
     * @return ItemRepository
     */
    private function getItemRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Item');
    }
}
