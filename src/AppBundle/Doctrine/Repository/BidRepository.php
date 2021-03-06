<?php

namespace AppBundle\Doctrine\Repository;

use AppBundle\Entity\Bid;
use AppBundle\Entity\Item;

/**
 * BidRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BidRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Bid $bid
     */
    public function create(Bid $bid)
    {
        $date = new \DateTime();
        $bid->setCreated($date);

        $this->_em->persist($bid);
        $this->_em->flush();
        $this->_em->clear();
    }

    /**
     * @param Item $item
     * @return Bid|null
     */
    public function getHighestBidder(Item $item)
    {
        $query = $this->createQueryBuilder('bid')
            ->where('bid.item = :item')
            ->orderBy('bid.sum', 'desc')
            ->setParameter('item', $item)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
