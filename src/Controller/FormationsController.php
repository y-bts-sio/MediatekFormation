<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des formations côté front-office.
 * 
 * @author emds
 */
class FormationsController extends AbstractController
{
    private const TEMPLATE_FORMATIONS = "pages/formations.html.twig";

    /**
     * Repository des formations
     *
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * Repository des catégories
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Constructeur du contrôleur
     *
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste de toutes les formations
     *
     * @return Response
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les formations selon un champ et un ordre, éventuellement dans une table liée
     *
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Filtre les formations selon une valeur contenue dans un champ (et table si précisée)
     *
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche les détails d'une formation
     *
     * @param int $id
     * @return Response
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
}

