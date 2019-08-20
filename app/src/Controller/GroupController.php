<?php
/**
 * Group controller.
 */

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
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
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\GroupRepository            $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
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
     * @param \App\Entity\Group $group Group entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="group_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Group $group): Response
    {
        return $this->render(
            'group/view.html.twig',
            ['group' => $group]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\GroupRepository            $repository Group repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="group_new",
     * )
     */
    public function new(Request $request, GroupRepository $repository): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setCreatedAt(new DateTime());
            $group->setUpdatedAt(new DateTime());
            $repository->save($group);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('group_index');
        }

        return $this->render(
            'group/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Group                          $group       Group entity
     * @param \App\Repository\GroupRepository            $repository Group repository
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
     *     name="group_edit",
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
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Group                          $group       Group entity
     * @param \App\Repository\GroupRepository            $repository Group repository
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
     *     name="group_delete",
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
     * @param Group $group
     * @param GroupRepository $repository Group Repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/newmember",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="group_member",
     * )
     */
    public function newMember(Group $group, GroupRepository $repository): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('security_login');
        }

        $check = $repository -> findOneBy(['user' => $this->getUser(), 'group' => $group]);
        if ($check instanceof Group) {
            $repository->delete($check);

            $this->addFlash('success', 'message.not_in_group');
        } else {
            $member = $group -> addUser($this->getUser());
            $repository->save($group);

            $this -> addFlash('success', 'message.voted_successfully');
        }

        return $this->redirectToRoute('group_view', ['id' => $group->getId()]);
    }
}

