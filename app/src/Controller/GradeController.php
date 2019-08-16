<?php
/**
 * Grade controller.
 */

namespace App\Controller;

use App\Entity\Grade;
use App\Form\GradeType;
use App\Repository\GradeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GradeController.
 *
 * @Route("/grade")
 */
class GradeController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\GradeRepository            $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="grade_index",
     * )
     */
    public function index(Request $request, GradeRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Grade::NUMBER_OF_ITEMS
        );

        return $this->render(
            'grade/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Grade $grade Grade entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="grade_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Grade $grade): Response
    {
        return $this->render(
            'grade/view.html.twig',
            ['grade' => $grade]
        );
    }
    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Grade                          $grade       Grade entity
     * @param \App\Repository\GradeRepository            $repository Grade repository
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
     *     name="grade_delete",
     * )
     */
    public function delete(Request $request, Grade $grade, GradeRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $grade, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($grade);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('grade_index');
        }

        return $this->render(
            'grade/delete.html.twig',
            [
                'form' => $form->createView(),
                'grade' => $grade,
            ]
        );
    }
}
