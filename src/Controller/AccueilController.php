<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur d'accueil du site.
 * 
 * Gère l'affichage de la page d'accueil et des CGU.
 */
class AccueilController extends AbstractController
{
    /**
     * Repository des formations.
     *
     * @var FormationRepository
     */
    private FormationRepository $repository;

    /**
     * Constructeur du contrôleur.
     *
     * @param FormationRepository $repository Le repository des formations.
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche la page d'accueil du site.
     *
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }

    /**
     * Affiche la page des conditions générales d'utilisation.
     *
     * @return Response
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}

