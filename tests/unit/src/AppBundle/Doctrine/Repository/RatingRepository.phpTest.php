<?php

namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\RatingRepository;
use AppBundle\Entity\Rating;

class RatingRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testCreate()
    {
        /** @var RatingRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(Rating::class);

        $rating = new Rating();
        $rating->setRate(1);
        $rating->setComment('aaa');
        $repository->create($rating);
        $this->tester->seeInRepository(
            Rating::class,
            [
                'comment' => 'aaa',
                'rate'    => 1,
            ]
        );
    }
}
