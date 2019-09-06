<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
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
 * Class CommentController.
 *
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param CommentRepository  $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="comment_index",
     * )
     * @isGranted(
     *     "ROLE_ADMIN",
     *     )
     */
    public function index(Request $request, CommentRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Comment::NUMBER_OF_ITEMS
        );

        return $this->render(
            'comment/index.html.twig',
            ['pagination' => $pagination]
        );
    }
    /**
     * View action.
    *
    * @param Comment $comment Comment entity
    *
    * @return Response HTTP response
    *
    * @Route(
    *     "/{id}",
    *     name="comment_view",
    *     requirements={"id": "[1-9]\d*"}
    *     )
     */
    public function view(Comment $comment): Response
    {
        return $this->render(
            'comment/view.html.twig',
            ['comment' => $comment]
        );
    }
    /**
     * Edit action.
     *
     * @param Request           $request
     * @param Comment           $comment    Comment entity
     *
     * @param CommentRepository $repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     name="comment_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user",
     * )

     */
    public function edit(Request $request, Comment $comment, CommentRepository $repository): Response
    {
        $form = $this->createForm(CommentType::class, $comment, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($comment);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'comment/edit.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
    /**
     * Delete action.
     *
     * @param Request           $request    HTTP request
     * @param Comment           $comment    Comment entity
     * @param CommentRepository $repository Comment repository
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
     *     name="comment_delete",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="comment",
     * )
     */
    public function delete(Request $request, Comment $comment, CommentRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($comment);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'comment/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
