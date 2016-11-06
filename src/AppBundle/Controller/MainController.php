<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $items = $this->getRepository()
            ->findBy(
                ['status' => 'selling'],
                ['auctionEnd' => 'desc'],
                4
            );

        return $this->render(
            'AppBundle:main:index.html.twig',
            [
                'items' => $items
            ]
        );
    }

    /**
     * @return Response
     */
    public function auctionsAction()
    {
        $items = $this->getRepository()
            ->findBy(
                ['status' => 'selling'],
                ['auctionEnd' => 'desc'],
                20
            );

        return $this->render(
            'AppBundle:main:auctions.html.twig',
            [
                'items' => $items
            ]
        );
    }

    /**
     * @param int $id
     * @return Response
     */
    public function auctionAction($id)
    {
        /** @var Item $item */
        $item = $this->getRepository()->findOneBy(['id' => $id, 'status' => 'selling']);
        $user = $this->get('user_manager')->getUser();

        return $this->render(
            'AppBundle:main:auction.html.twig',
            [
                'item' => $item,
                'user' => $user
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
