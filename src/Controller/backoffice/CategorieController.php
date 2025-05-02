<?php

namespace App\Controller\backoffice;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de gestion des catégories dans le back-office.
 */
class CategorieController extends AbstractController
{
    /**
     * Affiche la liste des catégories et permet l'ajout via un mini-formulaire.
     */
    #[Route('/admin/categories', name: 'backoffice_categories')]
    public function index(Request $request, EntityManagerInterface $em, CategorieRepository $categorieRepository, FormationRepository $formationRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $categorieRepository->findOneBy(['name' => $categorie->getName()]);
            if ($existing) {
                $this->addFlash('error', 'Cette catégorie existe déjà.');
            } else {
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('success', 'Catégorie ajoutée avec succès.');
                return $this->redirectToRoute('backoffice_categories');
            }
        }

        return $this->render('backoffice/categories/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'form' => $form->createView(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    /**
     * Supprime une catégorie si elle n'est liée à aucune formation.
     */
    #[Route('/admin/categories/delete/{id}', name: 'backoffice_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        if ($categorie->getFormations()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer une catégorie liée à des formations.');
        } elseif ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $em->remove($categorie);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('backoffice_categories');
    }
}
