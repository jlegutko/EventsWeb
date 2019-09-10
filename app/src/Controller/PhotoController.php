<?php
/**
 * Photo controller.
 */

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Event;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * Add a new photo action.
     *
     * @param Request         $request    HTTP request
     * @param Event           $event
     * @param PhotoRepository $repository Photo Repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/event/{id}/new",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_new_photo",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="event",
     * )
     */
    public function newPhoto(Request $request, Event $event, PhotoRepository $repository): Response
    {
        if ($event->getPhoto()) {
            $photo = $event->getPhoto();

            return $this->redirectToRoute(
                'photo_edit',
                ['id' => $photo->getId()]
            );
        }
        $photo = new Photo();
        $photo->setEvent($event);
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setEvent($event);
            $photo->setCreatedAt(new DateTime());
            $photo->setUpdatedAt(new DateTime());
            $repository->save($photo);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render(
            'event/new_photo.html.twig',
            ['form' => $form->createView(),
                'event' => $event,
            ]
        );
    }

    /**
     * View action.
     *
     * @param Photo $photo Photo entity
     *
     * @return Response HTTP response
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
     * @param Request         $request    HTTP  request
     * @param Photo           $photo      Photo entity
     * @param PhotoRepository $repository Photo repository
     * @param Filesystem      $filesystem Filesystem component
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
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
        if ($photo->getEvent()->getUser() === $this->getUser()) {
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
        } else {
            return $this->redirectToRoute(
                'event_index'
            );
        }
    }

    /**
     * Delete action.
     *
     * @param Request         $request    HTTP request
     * @param Photo           $photo      Photo entity
     * @param PhotoRepository $repository Photo repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="photo_delete",
     * )
     *
     */
    public function delete(Request $request, Photo $photo, PhotoRepository $repository): Response
    {
        if ($photo->getEvent()->getUser() === $this->getUser()) {
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
        } else {
            return $this->redirectToRoute(
                'event_index'
            );
        }
    }
}
