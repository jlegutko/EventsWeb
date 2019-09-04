<?php
/**
 * Search controller
 */

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** Class SearchController.
 * */
class SearchController extends AbstractController
{
    /**
     * Index action.
     * @param Request $request
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="search_index",
     * )
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search');
        if ($search) {
            if (preg_match('/^[\p{L}\p{N}_-]+$/u', $search)) {
                return $this -> redirectToRoute('search_action', ['search' => $search]);
            }
            $this -> addFlash('warning', 'message.invalid_characters');
        }

        return $this -> render(
            'search/index.html.twig'
        );
    }
    /**
     * Search action.
     *
     * @param string             $search
     * @param EventRepository    $eventRepository
     * @param UserRepository     $userRepository
     * @param CategoryRepository $categoryRepository
     * @param GroupRepository     $groupRepository
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/search/{search}",
     *     name="search_action",
     *     requirements={"search": "[\p{L}\p{N}_-]+"},
     * )
     */
    public function view(string $search, EventRepository $eventRepository, UserRepository $userRepository, CategoryRepository $categoryRepository, GroupRepository $groupRepository): Response
    {
        $eventNameResults = $eventRepository -> findByNamePart($search) -> getQuery() -> getResult();
        $eventStartDateResults = $eventRepository -> findByStartDatePart($search) -> getQuery() -> getResult();
        $eventEndDateResults = $eventRepository -> findByEndDatePart($search) -> getQuery() -> getResult();
        $eventPriceResults = $eventRepository -> findByPricePart($search) -> getQuery() -> getResult();
        $eventSizeResults = $eventRepository -> findBySizePart($search) -> getQuery() -> getResult();
        $eventPlaceResults = $eventRepository -> findByPlacePart($search) -> getQuery() -> getResult();
        $userNameResults = $userRepository -> findByUserNamePart($search) -> getQuery() -> getResult();
        $groupResults = $groupRepository -> findByGroupNamePart($search) -> getQuery() -> getResult();
        $categoryResults = $categoryRepository->findByCategoryNamePart($search) -> getQuery() -> getResult();

        return $this -> render(
            'search/view.html.twig',
            [
                'search' => $search,
                'events_names' => $eventNameResults,
                'events_start_dates' =>  $eventStartDateResults,
                'events_end_dates' => $eventEndDateResults,
                'events_prices' => $eventPriceResults,
                'events_sizes' => $eventSizeResults,
                'places' => $eventPlaceResults,
                'users' => $userNameResults,
                'groups' => $groupResults,
                'category' => $categoryResults,
            ]
        );
    }
}
