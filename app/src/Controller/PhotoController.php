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
}
