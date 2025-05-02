<?php

namespace App\Controller\backoffice;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de gestion des playlists pour le back-office.
 */
class PlaylistController extends AbstractController
{
    private PlaylistRepository $playlistRepository;
    private EntityManagerInterface $em;

    /**
     * Constructeur injectant les dépendances.
     */
    public function __construct(PlaylistRepository $playlistRepository, EntityManagerInterface $em)
    {
        $this->playlistRepository = $playlistRepository;
        $this->em = $em;
    }

    /**
     * Affiche la liste des playlists avec filtres et tris.
     */
    #[Route('/admin/playlists', name: 'backoffice_playlists')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('backoffice/playlists/index.html.twig', [
            'playlists' => $this->playlistRepository->findAllOrderByName('ASC'),
            'categories' => $categorieRepository->findAll(),
            'valeur' => null,
            'table' => null,
        ]);
    }

    /**
     * Trie les playlists par nom ou nombre de formations.
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'backoffice_playlists_sort')]
    public function sort(string $champ, string $ordre, CategorieRepository $categorieRepository): Response
    {
        $playlists = ($champ === 'formationCount')
            ? $this->playlistRepository->findAllOrderByFormationCount($ordre)
            : $this->playlistRepository->findAllOrderByName($ordre);

        return $this->render('backoffice/playlists/index.html.twig', [
            'playlists' => $playlists,
            'categories' => $categorieRepository->findAll(),
            'valeur' => null,
            'table' => null,
        ]);
    }

    /**
     * Recherche les playlists selon un champ et une table liée.
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'backoffice_playlists_findallcontain', methods: ['POST'])]
    public function findAllContain(string $champ, Request $request, CategorieRepository $categorieRepository, string $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);

        return $this->render('backoffice/playlists/index.html.twig', [
            'playlists' => $playlists,
            'categories' => $categorieRepository->findAll(),
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    /**
     * Ajoute une nouvelle playlist.
     */
    #[Route('/admin/playlists/new', name: 'backoffice_playlists_new')]
    public function new(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($playlist);
            $this->em->flush();
            $this->addFlash('success', 'Playlist ajoutée avec succès.');
            return $this->redirectToRoute('backoffice_playlists');
        }

        return $this->render('backoffice/playlists/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => false,
        ]);
    }

    /**
     * Modifie une playlist existante.
     */
    #[Route('/admin/playlists/edit/{id}', name: 'backoffice_playlists_edit')]
    public function edit(Request $request, Playlist $playlist): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Playlist modifiée avec succès.');
            return $this->redirectToRoute('backoffice_playlists');
        }

        return $this->render('backoffice/playlists/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => true,
            'formations' => $playlist->getFormations(),
        ]);
    }

    /**
     * Supprime une playlist si elle n’est liée à aucune formation.
     */
    #[Route('/admin/playlists/delete/{id}', name: 'backoffice_playlists_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist): Response
    {
        if ($playlist->getFormations()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer : des formations sont liées à cette playlist.');
        } elseif ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $this->em->remove($playlist);
            $this->em->flush();
            $this->addFlash('success', 'Playlist supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('backoffice_playlists');
    }
}
