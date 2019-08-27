<?php
/**
 * ProfilePhoto upload listener.
 */

namespace App\EventListener;

use App\Entity\ProfilePhoto;
use App\Service\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ProfilePhotoUploadListener.
 */
class ProfilePhotoUploadListener
{
    /**
     * Uploader service.
     *
     * @var \App\Service\FileUploader|null
     */
    protected $uploaderService = null;

    /**
     * ProfilePhotoUploadListener constructor.
     *
     * @param \App\Service\FileUploader $fileUploader File uploader service
     */
    public function __construct(FileUploader $fileUploader)
    {
        $this->uploaderService = $fileUploader;
    }

    /**
     * Pre persist.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args Event args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    /**
     * Pre update.
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $args Event args
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    /**
     * Upload file.
     *
     * @param \App\Entity\ProfilePhoto $entity ProfilePhoto entity
     *
     * @throws \Exception
     */
    private function uploadFile($entity): void
    {
        if (!$entity instanceof ProfilePhoto) {
            return;
        }

        $file = $entity->getFile();
        if ($file instanceof UploadedFile) {
            $filename = $this->uploaderService->upload($file);
            $entity->setFile($filename);
        }
    }
    /**
     * Post load.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args Event args
     *
     * @throws \Exception
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ProfilePhoto) {
            return;
        }

        if ($fileName = $entity->getFile()) {
            $entity->setFile(
                new File(
                    $this->uploaderService->getTargetDirectory().'/'.$fileName
                )
            );
        }
    }
    /**
     * Pre remove.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->removeFile($entity);
    }

    /**
     * Remove file from disk.
     *
     * @param ProfilePhoto $entity ProfilePhoto entity
     */
    private function removeFile($entity): void
    {
        if (!$entity instanceof ProfilePhoto) {
            return;
        }

        $file = $entity->getFile();
        if ($file instanceof File) {
            $filesystem = new Filesystem();
            $filesystem->remove($file);
        }
    }
}