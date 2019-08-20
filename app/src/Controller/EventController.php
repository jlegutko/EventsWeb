<?php
/**
 * Event controller.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Comment;
use App\Entity\Grade;
use App\Entity\User;
use App\Entity\Interest;
use App\Entity\Photo;
use App\Form\EventType;
use App\Form\CommentType;
use App\Form\GradeType;
use App\Form\PhotoType;
use App\Repository\EventRepository;
use App\Repository\CommentRepository;
use App\Repository\GradeRepository;
use App\Repository\InterestRepository;
use App\Repository\PhotoRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
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
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\EventRepository            $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
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
     * @param Event $event Event entity
     * @param Grade $grade Grade entity
     * @param User $user User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="event_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Event $event): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $event->form = $form->createView();
        $grade_new = new Grade();
        $form_grade = $this->createForm(GradeType::class, $grade_new);
        $event->form_grade = $form_grade->createView();
        $photo = $event->getPhoto();
        $grade = $event->getGrades();
        $user = $this->getUser();

        if ($photo === null) {
            return $this->render(
                'event/view.html.twig',
                ['event' => $event]
            );
        } else {
            return $this->render(
                'event/view.html.twig',
                ['event' => $event,
                 'photo' => $photo,
                 'grade' => $grade,
                  'user' => $user]
            );
        }
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\EventRepository            $repository Event repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Event                          $event       Event entity
     * @param \App\Repository\EventRepository            $repository Event repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_edit",
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
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Event                          $event       Event entity
     * @param \App\Repository\EventRepository            $repository Event repository
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
     *     name="event_delete",
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
     * Add a new comment action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param Event  $event
     * @param CommentRepository $repository Event Repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/newcomment",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_new_comment",
     * )
     */
    public function newComment(Request $request, Event $event, CommentRepository $repository): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTime());
            $comment->setUpdatedAt(new DateTime());
            $comment->setOwner($this->getUser());
            $comment->setEvent($event);
            $repository->save($comment);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render(
            'event/new_comment.html.twig',
            ['form' => $form->createView(),
             'event' => $event,
            ]
        );
    }
    /**
     * Add a new grade action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param Event  $event
     * @param GradeRepository $repository Event Repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }
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
            ]
        );
    }

    /**
     * Edit Grade action.
     *
     * @param \App\Entity\Grade $grade Grade entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/grade/{id}/edit",
     *     name="grade_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function editGrade(Request $request, Grade $grade, GradeRepository $repository): Response
    {
        $form = $this->createForm(GradeType::class, $grade, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($grade);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('grade_index');
        }

        return $this->render(
            'grade/edit.html.twig',
            [
                'form' => $form->createView(),
                'grade' => $grade,
            ]
        );
    }

    /**
     * Grades for an event.
     *
     * @param Event $event Event entity
     * @param GradeRepository $repository Grade Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}/allgrades",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="all_grades",
     *     )
     */
    public function grades(Request $request, Event $event, GradeRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByEvent($event),
            $request->query->getInt('page', 1),
            Event::NUMBER_OF_ITEMS
        );

        return $this->render(
            'event/grades.html.twig',
            ['pagination' => $pagination]
        );
    }
    /**
     * Interest action.
     *
     * @param Event $event
     * @param InterestRepository $repository Event Repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }

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
    /**
     * Add a new photo action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param Event  $event
     * @param Photo $photo
     * @param PhotoRepository $repository Event Repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/newphoto",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_new_photo",
     * )
     */
    public function newPhoto(Request $request, Event $event, PhotoRepository $repository): Response
    {
        if ($event->getPhoto()) {
            $photo = $event->getPhoto();

            return $this->redirectToRoute(
                'photo_edit',
                ['id' => $photo->getId()]
            );
        }
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setEvent($event);
            $photo->setCreatedAt(new DateTime());
            $photo->setUpdatedAt(new DateTime());
            $repository->save($photo);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render(
            'event/new_photo.html.twig',
            ['form' => $form->createView(),
             'event' => $event,
            ]
        );
    }
}
