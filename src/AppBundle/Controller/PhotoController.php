<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\Repository\ItemRepository;
use AppBundle\Doctrine\Repository\PhotoRepository;
use AppBundle\Entity\Item;
use AppBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends Controller
{
    /**
     * @param Request $request
     * @param integer $itemId
     * @return RedirectResponse|Response
     */
    public function uploadAction(Request $request, $itemId)
    {
        $importDirectory = $this->getParameter('directory.import.images');
        /** @var Item $item */
        $item = $this->getDoctrine()->getRepository('AppBundle:Item')->findOneBy(['id' => $itemId]);


        if ($this->get('user_manager')->getUser()->getId() != $item->getOwner()->getId()) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createFormBuilder()
            ->add(
                'file',
                FileType::class,
                ['required' => false]
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $request->files->get('file');
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($importDirectory, $fileName);

            $photo = new Photo();
            $photo->setPath($importDirectory);
            $photo->setName($fileName);
            $photo->setItem($item);

            $repository = $this->getRepository();
            $repository->save($photo);

            if (null === $item->getPhoto()) {
                $item->setPhoto($photo);
                /** @var ItemRepository $itemRepository */
                $itemRepository = $this->getDoctrine()->getRepository('AppBundle:Item');
                $itemRepository->update($item);
            }

            return new Response();
        }

        return $this->render(
            'AppBundle:photo:add.html.twig',
            [
                'form' => $form->createView(),
                'item' => $item
            ]
        );
    }

    /**
     * @return PhotoRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Photo');
    }
}
