<?php

namespace AppBundle\Doctrine\Repository;

use AppBundle\Entity\Item;

/**
 * ItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends \Doctrine\ORM\EntityRepository
{
    const STATUS_NEW = 'new';
    const STATUS_SELLING = 'selling';
    const STATUS_SOLD = 'sold';
    const STATUS_FINISHED = 'finished';
    const STATUS_BLOCKED = 'blocked';

    /**
     * @param Item $item
     */
    public function create(Item $item)
    {
        $date = new \DateTime();
        $item->setCreated($date);
        $item->setLastUpdated($date);
        $item->setStatus(static::STATUS_NEW);

        $this->_em->persist($item);
        $this->_em->flush();
        $this->_em->clear();
    }

    /**
     * @param Item $item
     * @param bool $flush
     */
    public function update(Item $item, $flush = true)
    {

        $date = new \DateTime();
        $item->setLastUpdated($date);

        $this->_em->persist($item);
        if (true == $flush) {
            $this->_em->flush();
            $this->_em->clear();
        }
    }

    /**
     * @param Item[] $items
     */
    public function updateArray($items)
    {
        foreach ($items as $item) {
            $this->update($item, false);
        }

        $this->_em->flush();
        $this->_em->clear();
    }

    /**
     * @param null|int $limit
     * @return array
     */
    public function getActive($limit = null)
    {
        $now = new \DateTime();

        $builder = $this->createQueryBuilder('item')
            ->leftJoin('item.owner', 'owner')
            ->where('item.status = :status')
            ->andWhere('owner.active = 1')
            ->setParameter('status', static::STATUS_SELLING)
            ->setParameter('now', $now->format('Y-m-d H:i:s'))
            ->orderBy('item.created', 'asc');

        $expr = $builder->expr()->andX('item.auctionStart <= :now', 'item.auctionEnd >= :now');
        $expr = $builder->expr()->orX($expr, 'item.auctionStart IS NULL', 'item.auctionEnd IS NULL');
        $builder->andWhere($expr);

        if (null !== $limit) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param null|int $limit
     * @return array
     */
    public function getNew($limit = null)
    {
        $builder = $this->createQueryBuilder('item')
            ->where('item.status = :status')
            ->setParameter('status', static::STATUS_NEW)
            ->orderBy('item.created', 'asc');

        if (null !== $limit) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()->getResult();
    }
}
