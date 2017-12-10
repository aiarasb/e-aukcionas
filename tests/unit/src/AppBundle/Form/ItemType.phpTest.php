<?php

namespace src\AppBundle\Form;


use AppBundle\Entity\Item;
use AppBundle\Form\ItemType;
use Symfony\Component\Form\Test\TypeTestCase;

class ItemTypeTest extends TypeTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testSubmitValidData()
    {
        $formData = [
            'name'         => 'item',
            'description'  => 'item desc',
            'basePrice'    => 10,
            'buyNowPrice'  => 20,
            'auctionStart' => '2016-01-01 01:01:01',
            'auctionEnd'   => '2016-01-10 10:10:10',
        ];

        $form = $this->factory->create(ItemType::class);

        $object = new Item();
        $object->setName('item');
        $object->setDescription('item desc');
        $object->setBasePrice(10);
        $object->setBuyNowPrice(20);
        $object->setAuctionStart(new \DateTime('2016-01-01 01:01:01'));
        $object->setAuctionEnd(new \DateTime('2016-01-10 10:10:10'));

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
