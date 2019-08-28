<?php
/**
 * Discussion controller.
 */

namespace App\Controller;

use DateTime;
use App\Entity\Discussion;
use App\Form\DiscussionType;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\DiscussionRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DiscussionController.
 *
 * @Route("/discussion")
 */
class DiscussionController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request              $request    HTTP request
     * @param DiscussionRepository $repository Repository
     * @param PaginatorInterface   $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="discussion_index",
     * )
     */
    public function index(Request $request, DiscussionRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Discussion::NUMBER_OF_ITEMS
        );

        return $this->render(
            'discussion/index.html.twig',
            ['pagination' => $pagination]
        );
    }
    /**
     * View action.
     *
     * @param Discussion $discussion Discussion entity
     * @param Post       $post
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="discussion_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Discussion $discussion, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $discussion->form = $form->createView();

        return $this->render(
            'discussion/view.html.twig',
            ['discussion' => $discussion]
        );
    }
    /**
     * Edit action.
     *
     * @param Request              $request
     * @param Discussion           $discussion Discussion entity
     *
     * @param DiscussionRepository $repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     name="discussion_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function edit(Request $request, Discussion $discussion, DiscussionRepository $repository): Response
    {
        $form = $this->createForm(DiscussionType::class, $discussion, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($discussion);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('discussion_index');
        }

        return $this->render(
            'discussion/edit.html.twig',
            [
                'form' => $form->createView(),
                'discussion' => $discussion,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request              $request    HTTP request
     * @param Discussion           $discussion Discussion entity
     * @param DiscussionRepository $repository Discussion repository
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
     *     name="discussion_delete",
     * )
     */
    public function delete(Request $request, Discussion $discussion, DiscussionRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $discussion, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($discussion);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('discussion_index');
        }

        return $this->render(
            'discussion/delete.html.twig',
            [
                'form' => $form->createView(),
                'discussion' => $discussion,
            ]
        );
    }
    /**
     * Add a new post action.
     *
     * @param Request        $request    HTTP request
     * @param Discussion     $discussion
     *
     * @param PostRepository $repository Post repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/newpost",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="discussion_new_post",
     * )
     */
    public function newPost(Request $request, Discussion $discussion, PostRepository $repository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new DateTime());
            $post->setUpdatedAt(new DateTime());
            $post->setDiscussion($discussion);
            $post->setUser($this->getUser());
            $repository->save($post);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('discussion_view', ['id' => $discussion->getId()]);
        }

        return $this->render(
            'discussion/new_post.html.twig',
            ['form' => $form->createView(),
                'discussion' => $discussion,
            ]
        );
    }
}
