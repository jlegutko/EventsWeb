<?php
/**
 * Post controller.
 */

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController.
 *
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param PostRepository     $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="post_index",
     * )
     */
    public function index(Request $request, PostRepository $repository, PaginatorInterface $paginator): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Post::NUMBER_OF_ITEMS
        );

        return $this->render(
            'post/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param Post $post Post entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="post_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Post $post): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }
        return $this->render(
            'post/view.html.twig',
            ['post' => $post]
        );
    }

    /**
     * Edit action.
     *
     * @param Request        $request
     * @param Post           $post       Post entity
     *
     * @param PostRepository $repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     name="post_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="post",
     * )
     */
    public function edit(Request $request, Post $post, PostRepository $repository): Response
    {
        $form = $this->createForm(PostType::class, $post, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($post);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request        $request    HTTP request
     * @param Post           $post       Post entity
     * @param PostRepository $repository Post repository
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
     *     name="post_delete",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="post",
     * )
     */
    public function delete(Request $request, Post $post, PostRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $post, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($post);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/delete.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }
}
