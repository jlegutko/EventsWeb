<?php
/**
 * Event controller.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Comment;
use App\Entity\Grade;
use App\Entity\Interest;
use App\Form\EventType;
use App\Form\CommentType;
use App\Form\GradeType;
use App\Repository\EventRepository;
use App\Repository\GradeRepository;
use App\Repository\InterestRepository;
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
 * Class EventController.
 *
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param EventRepository    $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="event_index",
     * )
     */
    public function index(Request $request, EventRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Event::NUMBER_OF_ITEMS
        );

        return $this->render(
            'event/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param Event              $event      Event entity
     * @param GradeRepository    $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @param Request            $request
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="event_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Event $event, GradeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $event->form = $form->createView();
        $gradeNew = new Grade();
        $formGrade = $this->createForm(GradeType::class, $gradeNew);
        $event->formGrade = $formGrade->createView();
        $photo = $event->getPhoto();
        $grade = $event->getGrades();
        $user = $this->getUser();
        $check = $paginator->paginate(
            $repository->queryByOwnerAndEvent($user, $event),
            $request->query->getInt('page', 1),
            Event::NUMBER_OF_ITEMS
        );
        $allGrades = $paginator->paginate(
            $repository->queryByEvent($event),
            $request->query->getInt('page', 1),
            Event::NUMBER_OF_ITEMS
        );

        return ($photo === null) ? $this->render(
            'event/view.html.twig',
            ['event' => $event,
                'grade' => $grade,
                'user' => $user,
                'check' => $check,
                'all_grades' => $allGrades, ]
        ) : $this->render(
            'event/view.html.twig',
            ['event' => $event,
                'photo' => $photo,
                'grade' => $grade,
                'user' => $user,
                'check' => $check,
                'all_grades' => $allGrades, ]
        );
    }

    /**
     * New action.
     *
     * @param Request         $request    HTTP request
     * @param EventRepository $repository Event repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="event_new",
     * )
     */
    public function new(Request $request, EventRepository $repository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUser($this->getUser());
            $event->setCreatedAt(new DateTime());
            $event->setUpdatedAt(new DateTime());
            $repository->save($event);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'event/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request         $request    HTTP request
     * @param Event           $event      Event entity
     * @param EventRepository $repository Event repository
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
     *     name="event_edit",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="event",
     * )
     */
    public function edit(Request $request, Event $event, EventRepository $repository): Response
    {
        $form = $this->createForm(EventType::class, $event, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($event);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'event/edit.html.twig',
            [
                'form' => $form->createView(),
                'event' => $event,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request         $request    HTTP request
     * @param Event           $event      Event entity
     * @param EventRepository $repository Event repository
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
     *     name="event_delete",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="event",
     * )
     */
    public function delete(Request $request, Event $event, EventRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $event, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($event);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('event_index');
        }

        return $this->render(
            'event/delete.html.twig',
            [
                'form' => $form->createView(),
                'event' => $event,
            ]
        );
    }

    /**
     * Interest action.
     *
     * @param Event              $event
     * @param InterestRepository $repository Event Repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/interest",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_interest",
     * )
     */
    public function interest(Event $event, InterestRepository $repository): Response
    {
        $check = $repository -> findOneBy(['user' => $this->getUser(), 'event' => $event]);
        if ($check instanceof Interest) {
            $repository->delete($check);

            $this->addFlash('success', 'message.uninterested');
        } else {
            $interest = new Interest();
            $interest->setUser($this->getUser());
            $interest->setEvent($event);
            $repository->save($interest);

            $this -> addFlash('success', 'message.voted_successfully');
        }

        return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
    }
}
