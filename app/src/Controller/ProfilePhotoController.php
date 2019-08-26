<?php
/**
 * ProfilePhoto controller.
 */

namespace App\Controller;

use App\Entity\ProfilePhoto;
use App\Form\ProfilePhotoType;
use App\Repository\ProfilePhotoRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(FileUploader $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\ProfilePhotoRepository           $repository ProfilePhoto repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="profile_photo_new",
     * )
     */
    public function new(Request $request, ProfilePhotoRepository $repository): Response
    {
        $profile_photo = new ProfilePhoto();
        $form = $this->createForm(ProfilePhotoType::class, $profile_photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile_photo->setUser($this->getUser());
            $repository->save($profile_photo);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'profile_photo/new.html.twig',
            ['form' => $form->createView()]
        );
    }
}