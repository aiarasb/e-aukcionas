<?php
namespace src\AppBundle\Doctrine\Repository;


use AppBundle\Doctrine\Repository\CommentRepository;
use AppBundle\Entity\Comment;

class CommentRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testCreate()
    {
        /** @var CommentRepository $repository */
        $repository = $this->getModule('Doctrine2')->em->getRepository(Comment::class);

        $comment = new Comment();
        $comment->setComment('aaa');
        $repository->create($comment);
        $this->tester->seeInRepository(
            Comment::class,
            [
                'comment' => 'aaa',
            ]
        );
    }
}
