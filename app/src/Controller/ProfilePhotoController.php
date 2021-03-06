<?php
/**
 * ProfilePhoto controller.
 */
namespace App\Controller;

use App\Entity\ProfilePhoto;
use App\Entity\User;
use App\Form\ProfilePhotoType;
use App\Repository\ProfilePhotoRepository;
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
 * Class ProfilePhotoController.
 *
 * @Route("/profile_photo")
 */
class ProfilePhotoController extends AbstractController
{
    private $uploaderService = null;
    /**
     * ProfilePhotoController constructor.
     * @param FileUploader $uploaderService
     */
    public function __construct(FileUploader $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }
    /**
     * Add a new profile photo action.
     *
     * @param Request                $request    HTTP request
     * @param User                   $user
     * @param ProfilePhotoRepository $repository ProfilePhoto Repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/users/{id}/newphoto",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="new_profile_photo",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user",
     * )
     */
    public function newPhoto(Request $request, User $user, ProfilePhotoRepository $repository): Response
    {
        if ($user->getProfilePhoto()) {
            $profilePhoto = $user->getProfilePhoto();

            return $this->redirectToRoute(
                'profile_photo_edit',
                ['id' => $profilePhoto->getId()]
            );
        }
        $profilePhoto = new ProfilePhoto();
        $profilePhoto -> setUser($user);
        $form = $this->createForm(ProfilePhotoType::class, $profilePhoto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profilePhoto->setUser($user);
            $profilePhoto->setCreatedAt(new DateTime());
            $profilePhoto->setUpdatedAt(new DateTime());
            $repository->save($profilePhoto);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('user_view', ['id' => $user->getId()]);
        }

        return $this->render(
            'registration/new_profile_photo.html.twig',
            ['form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
    /**
     * View action.
     *
     * @param ProfilePhoto $profilePhoto ProfilePhoto entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="profile_photo_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(ProfilePhoto $profilePhoto): Response
    {
        return $this->render(
            'profile_photo/view.html.twig',
            ['profile_photo' => $profilePhoto]
        );
    }
    /**
     * Edit action.
     *
     * @param Request                $request      HTTP  request
     * @param ProfilePhoto           $profilePhoto ProfilePhoto entity
     * @param ProfilePhotoRepository $repository   ProfilePhoto repository
     * @param Filesystem             $filesystem   Filesystem component
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
     *     name="profile_photo_edit",
     * )
     */
    public function edit(Request $request, ProfilePhoto $profilePhoto, ProfilePhotoRepository $repository, Filesystem $filesystem): Response
    {
        if ($profilePhoto->getUser() === $this->getUser()) {
            $originalProfilePhoto = clone $profilePhoto;
            $form = $this->createForm(ProfilePhotoType::class, $profilePhoto, ['method' => 'PUT']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $formData = $form->getData();
                if ($formData->getFile() instanceof UploadedFile) {
                    $repository->save($profilePhoto);
                    $file = $originalProfilePhoto->getFile();
                    $filesystem->remove($file->getPathname());
                }
                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute(
                    'profile_photo_view',
                    ['id' => $profilePhoto->getId()]
                );
            }

            return $this->render(
                'profile_photo/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'profile_photo' => $profilePhoto,
                ]
            );
        } else {
            return $this->redirectToRoute(
                'user_index'
            );
        }
    }
    /**
     * Delete action.
     *
     * @param Request                $request      HTTP request
     * @param ProfilePhoto           $profilePhoto ProfilePhoto entity
     * @param ProfilePhotoRepository $repository   ProfilePhoto repository
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
     *     name="profile_photo_delete",
     * )
     */
    public function delete(Request $request, ProfilePhoto $profilePhoto, ProfilePhotoRepository $repository): Response
    {
        if ($profilePhoto->getUser() === $this->getUser()) {
            $form = $this->createForm(FormType::class, $profilePhoto, ['method' => 'DELETE']);
            $form->handleRequest($request);
            if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
                $form->submit($request->request->get($form->getName()));
            }
            if ($form->isSubmitted() && $form->isValid()) {
                $repository->delete($profilePhoto);
                $this->addFlash('success', 'message.deleted_successfully');

                return $this->redirectToRoute('event_index');
            }

            return $this->render(
                'profile_photo/delete.html.twig',
                [
                    'form' => $form->createView(),
                    'profile_photo' => $profilePhoto,
                ]
            );
        } else {
            return $this->redirectToRoute(
                'user_index'
            );
        }
    }
}
