<?php

namespace App\Controller\backoffice;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de gestion des formations (Back-office).
 */
class FormationController extends AbstractController
{
    private FormationRepository $formationRepository;
    private EntityManagerInterface $em;

    /**
     * Constructeur.
     */
    public function __construct(FormationRepository $formationRepository, EntityManagerInterface $em)
    {
        $this->formationRepository = $formationRepository;
        $this->em = $em;
    }

    /**
     * Affiche la liste des formations avec filtres et tris.
     */
    #[Route('/admin/formations', name: 'backoffice_formations')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $categorieRepository->findAll();

        return $this->render('backoffice/formations/index.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => null,
            'table' => null,
        ]);
    }

    /**
     * Trie les formations selon un champ et une table liée.
     */
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'backoffice_formations_sort')]
    public function sort(string $champ, string $ordre, CategorieRepository $categorieRepository, string $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $categorieRepository->findAll();

        return $this->render('backoffice/formations/index.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => null,
            'table' => $table,
        ]);
    }

    /**
     * Filtre les formations contenant une valeur spécifique.
     */
    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'backoffice_formations_findallcontain', methods: ['POST'])]
    public function findAllContain(string $champ, Request $request, CategorieRepository $categorieRepository, string $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $categorieRepository->findAll();

        return $this->render('backoffice/formations/index.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    /**
     * Ajoute une nouvelle formation.
     */
    #[Route('/admin/formations/new', name: 'backoffice_formations_new')]
    public function new(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $today = new \DateTime();
            if ($formation->getPublishedAt() > $today) {
                $this->addFlash('error', 'La date ne peut pas être postérieure à aujourd\'hui.');
            } else {
                $this->em->persist($formation);
                $this->em->flush();
                $this->addFlash('success', 'Formation ajoutée avec succès.');
                return $this->redirectToRoute('backoffice_formations');
            }
        }

        return $this->render('backoffice/formations/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => false,
        ]);
    }

    /**
     * Modifie une formation existante.
     */
    #[Route('/admin/formations/edit/{id}', name: 'backoffice_formations_edit')]
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $today = new \DateTime();
            if ($formation->getPublishedAt() > $today) {
                $this->addFlash('error', 'La date ne peut pas être postérieure à aujourd\'hui.');
            } else {
                $this->em->flush();
                $this->addFlash('success', 'Formation modifiée avec succès.');
                return $this->redirectToRoute('backoffice_formations');
            }
        }

        return $this->render('backoffice/formations/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => true,
        ]);
    }

    /**
     * Supprime une formation.
     */
    #[Route('/admin/formations/delete/{id}', name: 'backoffice_formations_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $this->em->remove($formation);
            $this->em->flush();
            $this->addFlash('success', 'Formation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        return $this->redirectToRoute('backoffice_formations');
    }
}
