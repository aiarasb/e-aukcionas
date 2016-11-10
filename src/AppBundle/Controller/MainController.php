<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\BidRepository;
use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Bid;
use AppBundle\Entity\Item;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param int      $id
     * @return Response
     */
    public function auctionAction(Request $request, $id)
    {
        $bidSuccess = null;
        /** @var Item $item */
        $item = $this->getRepository()->findOneBy(['id' => $id, 'status' => 'selling']);
        /** @var User $user */
        $user = $this->get('user_manager')->getUser();
        $commentForm = $this->createForm(
            CommentType::class,
            null,
            [
                'action' => $this->generateUrl('add_comment', ['id' => $id])
            ]
        );

        if ($request->getMethod() == 'POST'
            && $user->getId() != $item->getOwner()->getId()) {
            $bidData = $request->request->get('bidForm');
            if (!empty($bidData['bid'])) {
                $bidSum = $bidData['bid'];
                if ($bidSum > $item->getCurrentPrice()) {
                    $item->setCurrentPrice($bidSum);
                    $bid = new Bid();
                    $bid->setItem($item);
                    $bid->setUser($user);
                    $bid->setSum($bidSum);
                    $this->saveBid($bid, $item);
                    $bidSuccess = true;
                } else {
                    $bidSuccess = false;
                }
            }
        }

        return $this->render(
            'AppBundle:main:auction.html.twig',
            [
                'item'        => $item,
                'user'        => $user,
                'commentForm' => $commentForm->createView(),
                'bidSuccess'  => $bidSuccess
            ]
        );
    }

    /**
     * @param Bid  $bid
     * @param Item $item
     */
    private function saveBid(Bid $bid, Item $item)
    {
        $itemRepository = $this->getRepository();
        $itemRepository->update($item, false);
        /** @var BidRepository $bidRepository */
        $bidRepository = $this->getDoctrine()->getRepository('AppBundle:Bid');
        $bidRepository->create($bid);
    }

    /**
     * @return ItemRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Item');
    }
}
