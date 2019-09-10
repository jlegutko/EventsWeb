<?php
/**
 * Group controller.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Form\GroupType;
use App\Repository\GroupRepository;
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
 * Class GroupController.
 *
 * @Route("/group")
 */
class GroupController extends AbstractController
{
    /**
     * Add a new group action.
     *
     * @param Request         $request    HTTP request
     * @param GroupRepository $repository Group repository
     *
     * @param Event           $event
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/newgroup",
     *     methods={"GET", "POST"},
     *     name="event_new_group",
     * )
     */
    public function newGroup(Request $request, Event $event, GroupRepository $repository): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setCreatedAt(new DateTime());
            $group->setUpdatedAt(new DateTime());
            $group->setEvent($event);
            $group->setAuthor($this->getUser());
            $repository->save($group);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render(
            'event/new_group.html.twig',
            ['form' => $form->createView(),
                'event' => $event,
            ]
        );
    }
    /**
     * Index action.
     *
     * @param Request            $request    HTTP request
     * @param GroupRepository    $repository Repository
     * @param PaginatorInterface $paginator  Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="group_index",
     * )
     */
    public function index(Request $request, GroupRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Group::NUMBER_OF_ITEMS
        );

        return $this->render(
            'group/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param Request $request HTTP request
     *
     * @param Group   $group   Group entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="group_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Group $group, Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        return $this->render(
            'group/view.html.twig',
            ['group' => $group,
                'form' => $form->createView(),
                ]
        );
    }

    /**
     * Edit action.
     *
     * @param Request         $request    HTTP request
     * @param Group           $group      Group entity
     * @param GroupRepository $repository Group repository
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
     *     name="group_edit",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="group",
     * )
     */
    public function edit(Request $request, Group $group, GroupRepository $repository): Response
    {
        $form = $this->createForm(GroupType::class, $group, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($group);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('group_index');
        }

        return $this->render(
            'group/edit.html.twig',
            [
                'form' => $form->createView(),
                'group' => $group,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request         $request    HTTP request
     * @param Group           $group      Group entity
     * @param GroupRepository $repository Group repository
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
     *     name="group_delete",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="group",
     * )
     */
    public function delete(Request $request, Group $group, GroupRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $group, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($group);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('group_index');
        }

        return $this->render(
            'group/delete.html.twig',
            [
                'form' => $form->createView(),
                'group' => $group,
            ]
        );
    }
    /**
     * Adding a new member of group.
     *
     * @param Group            $group
     * @param MemberRepository $repository Member Repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/newmember",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="group_member",
     * )
     */
    public function newMember(Group $group, MemberRepository $repository): Response
    {
        $check = $repository -> findOneBy(['member' => $this->getUser(), 'community' => $group]);
        if ($check instanceof Member) {
            $repository->delete($check);

            $this->addFlash('success', 'message.not_member');
        } else {
            $member = new Member();
            $member->setMember($this->getUser());
            $member->setCommunity($group);
            $repository->save($member);

            $this -> addFlash('success', 'message.member_successfully');
        }

        return $this->redirectToRoute('group_view', ['id' => $group->getId()]);
    }

    /**
     * Shows groups connected with event.
     * @param Request            $request   HTTP request
     * @param Event              $event     Event entity
     * @param PaginatorInterface $paginator Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}/groups",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="event_groups",
     * )
     *
     */
    public function eventGroups(Request $request, Event $event, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $event->getGroups(),
            $request->query->getInt('page', 1),
            Group::NUMBER_OF_ITEMS
        );

        return $this->render(
            'event/groups.html.twig',
            ['pagination' => $pagination]
        );
    }
}
