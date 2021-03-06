<?php
/**
 * Grade controller.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Grade;
use App\Form\GradeType;
use App\Repository\GradeRepository;
use DateTime;
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
 * Class GradeController.
 *
 * @Route("/grade")
 */
class GradeController extends AbstractController
{
    /**
     * Add a new grade action.
     *
     * @param Request         $request    HTTP request
     * @param Event           $event
     * @param GradeRepository $repository Event Repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/newgrade",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_new_grade",
     * )
     */
    public function newGrade(Request $request, Event $event, GradeRepository $repository): Response
    {
        $check = $repository -> findOneBy(['user' => $this->getUser(), 'event' => $event]);
        if ($check instanceof Grade) {
            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        } else {
            $grade = new Grade();
            $form = $this->createForm(GradeType::class, $grade);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $grade->setCreatedAt(new DateTime());
                $grade->setUpdatedAt(new DateTime());
                $grade->setUser($this->getUser());
                $grade->setEvent($event);
                $repository->save($grade);

                $this->addFlash('success', 'message.created_successfully');

                return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
            }
        }

        return $this->render(
            'event/new_grade.html.twig',
            ['form' => $form->createView(),
                'event' => $event,
                'grade' => $grade,
            ]
        );
    }
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param GradeRepository    $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
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
     * @param Grade $grade Grade entity
     *
     * @return Response HTTP response
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
     * @param Request         $request    HTTP request
     * @param Grade           $grade      Grade entity
     * @param GradeRepository $repository Grade repository
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
     *     name="grade_delete",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="grade",
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

            return $this->redirectToRoute('event_index');
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
