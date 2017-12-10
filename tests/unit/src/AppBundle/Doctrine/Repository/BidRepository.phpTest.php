<?php

namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\BidRepository;
use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Bid;
use AppBundle\Entity\Item;

class BidRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function test()
    {
        /** @var BidRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(Bid::class);
        $itemId = $this->tester->haveInRepository(
            Item::class,
            [
                'name'        => 'item',
                'created'     => new \DateTime(),
                'lastUpdated' => new \DateTime(),
                'status'      => ItemRepository::STATUS_SELLING,
            ]
        );

        $item = $this->getModule('Doctrine2')->em->getRepository(Item::class)->find($itemId);
        $bid = new Bid();
        $bid->setItem($item);
        $bid->setSum(10);
        $repository->create($bid);
        $this->tester->seeInRepository(
            Bid::class,
            [
                'sum' => 10,
            ]
        );

        $item = $this->getModule('Doctrine2')->em->getRepository(Item::class)->find($itemId);
        $bid = new Bid();
        $bid->setItem($item);
        $bid->setSum(20);
        $repository->create($bid);
        $this->tester->seeInRepository(
            Bid::class,
            [
                'sum' => 20,
            ]
        );

        $this->assertEquals(20, $repository->getHighestBidder($item)->getSum());
    }
}
