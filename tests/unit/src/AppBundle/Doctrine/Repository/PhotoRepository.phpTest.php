<?php

namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\PhotoRepository;
use AppBundle\Entity\Photo;

class PhotoRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testCreate()
    {
        /** @var PhotoRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(Photo::class);

        $photo = new Photo();
        $photo->setName('photo');
        $photo->setPath('path-to-photo');
        $repository->save($photo);
        $this->tester->seeInRepository(
            Photo::class,
            [
                'name' => 'photo',
                'path' => 'path-to-photo',
            ]
        );
    }
}
