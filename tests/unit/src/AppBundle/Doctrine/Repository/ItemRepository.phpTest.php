<?php

namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Entity\Item;
use AppBundle\Entity\User;

class ItemRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveInRepository(
            'AppBundle\Entity\User',
            [
                'username'    => 'b',
                'email'       => 'b@b.b',
                'password'    => md5('b'),
                'firstName'   => 'B',
                'lastName'    => 'B',
                'created'     => new \DateTime(),
                'lastUpdated' => new \DateTime(),
                'role'        => 'user',
                'active'      => true,
            ]
        );
    }

    // tests
    public function test()
    {
        /** @var ItemRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(Item::class);

        $item = new Item();
        $item->setName('item1');
        $item->setAuctionStart(new \DateTime('2016-01-01'));
        $item->setAuctionEnd(new \DateTime('2016-01-10'));
        $repository->create($item);
        $this->tester->seeInRepository(
            Item::class,
            [
                'name' => 'item1',
            ]
        );

        $item = new Item();
        $item->setName('item2');
        $item->setAuctionStart(new \DateTime('yesterday'));
        $item->setAuctionEnd(new \DateTime('tomorrow'));
        $repository->create($item);
        $this->tester->seeInRepository(
            Item::class,
            [
                'name' => 'item2',
            ]
        );

        /** @var Item $item */
        $item = $repository->findOneBy(['name' => 'item2']);
        $user = $this->getModule('Doctrine2')->em->getRepository(User::class)->findOneBy(['username' => 'b']);
        $item->setDescription('item2 desc');
        $item->setOwner($user);
        $repository->update($item);
        $this->tester->seeInRepository(
            Item::class,
            [
                'description' => 'item2 desc',
            ]
        );

        $newItems = $repository->getNew(5);
        $this->assertCount(2, $newItems);

        $newItems = $repository->getNew(1);
        $this->assertCount(1, $newItems);

        $items = $repository->findAll();
        /** @var Item $item */
        foreach ($items as $item) {
            $item->setStatus(ItemRepository::STATUS_SELLING);
        }
        $repository->updateArray($items);
        $this->tester->seeInRepository(
            Item::class,
            [
                'status' => ItemRepository::STATUS_SELLING,
            ]
        );

        $activeItems = $repository->getActive(5);
        $this->assertCount(1, $activeItems);
    }
}
