<?php
/**
 * Registration controller.
 */
namespace App\Controller;

use App\Entity\User;
use App\Entity\ProfilePhoto;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Form\ProfilePhotoType;
use App\Repository\UserRepository;
use App\Repository\ProfilePhotoRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use \Exception;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param UserRepository     $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route (
     *     "/users",
     *     name="user_index",
     * )
     * @isGranted(
     *     "ROLE_ADMIN",
     *     )
     */
    public function index(Request $request, UserRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            User::NUMBER_OF_ITEMS
        );

        return $this->render(
            'registration/index.html.twig',
            ['pagination' => $pagination]
        );
    }
    /**
     * View action.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/users/{id}",
     *     name="user_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(User $user): Response
    {
        return $this->render(
            'registration/view.html.twig',
            ['user' => $user]
        );
    }
    /**
     * New action.
     *
     * @param Request                      $request         HTTP request
     * @param UserRepository               $repository      User repository
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     *
     * @Route(
     *     "/register",
     *     methods={"GET", "POST"},
     *     name="user_new",
     * )
     */
    public function new(Request $request, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('user_view', ['id' => $this->getUser()->getId()]);
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());
            $password = $user->getPassword();
            $user->setRoles(['ROLE_USER']);
            $password = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($password);
            $repository->save($user);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('security_login');

        }

        return $this->render(
            'registration/new.html.twig',
            ['form' => $form->createView()]
        );
    }
    /**
     * Edit action.
     *
     * @param Request        $request    HTTP request
     * @param User           $user       User entity
     * @param UserRepository $repository User repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/users/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_edit",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user",
     * )

     */
    public function edit(Request $request, User $user, UserRepository $repository): Response
    {
        $form = $this->createForm(UserType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($user);
            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'registration/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Change password action.
     *
     * @param Request                      $request         HTTP request
     * @param User                         $user            User entity
     * @param UserRepository               $repository      User repository
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/users/{id}/change_pswd",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_change_pswd",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user",
     * )

     */
    public function changePassword(Request $request, User $user, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'put']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTime());
            $password = $user->getPassword();
            $password = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($password);
            $repository->save($user);
            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('user_view');
        }

        return $this->render(
            'registration/change_pswd.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request        $request    HTTP request
     * @param User           $user       User entity
     * @param UserRepository $repository User repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/users/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_delete",
     * )
     */
    public function delete(Request $request, User $user, UserRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $user, ['method' => 'DELETE']);
        $form->handleRequest($request);
        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($user);
            $this->addFlash('success', 'message.deleted_successfully');
            $this->get('security.token_storage')->setToken(null);

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'registration/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
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
     * Change user's role.
     *
     * @param Request        $request    HTTP request
     * @param User           $user       User entity
     * @param UserRepository $repository User repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/changerole",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_change_role",
     * )
     *
     * @IsGranted(
     *     "ROLE_ADMIN",
     * )
     */
    public function changeRole(Request $request, User $user, UserRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $user, ['method' => 'PUT', 'validation_groups' => ['userChangeRole']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            }
            $repository->save($user);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('user_view', ['id' => $user->getId()]);
        }

        return $this->render(
            'registration/change_role.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
