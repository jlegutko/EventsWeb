<?php
/**
 * Photo controller.
 */

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PhotoController.
 *
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{
    private $uploaderService = null;

    /**
     * PhotoController constructor.
     * @param FileUploader $uploaderService
     */
    public function __construct(FileUploader $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }
    /**
     * View action.
     *
     * @param \App\Entity\Photo $photo Photo entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="photo_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Photo $photo): Response
    {
        return $this->render(
            'photo/view.html.twig',
            ['photo' => $photo]
        );
    }
    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP  request
     * @param \App\Entity\Photo                         $photo      Photo entity
     * @param \App\Repository\PhotoRepository           $repository Photo repository
     * @param \Symfony\Component\Filesystem\Filesystem  $filesystem Filesystem component
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="photo_edit",
     * )
     */
    public function edit(Request $request, Photo $photo, PhotoRepository $repository, Filesystem $filesystem): Response
    {
        $originalPhoto = clone $photo;

        $form = $this->createForm(PhotoType::class, $photo, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($formData->getFile() instanceof UploadedFile) {
                $repository->save($photo);
                $file = $originalPhoto->getFile();
                $filesystem->remove($file->getPathname());
            }

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute(
                'photo_view',
                ['id' => $photo->getId()]
            );
        }

        return $this->render(
            'photo/edit.html.twig',
            [
                'form' => $form->createView(),
                'photo' => $photo,
            ]
        );
    }
    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Photo                         $photo      Photo entity
     * @param \App\Repository\PhotoRepository           $repository Photo repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="photo_delete",
     * )
     */
    public function delete(Request $request, Photo $photo, PhotoRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $photo, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($photo);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'photo/delete.html.twig',
            [
                'form' => $form->createView(),
                'photo' => $photo,
            ]
        );
    }
}
