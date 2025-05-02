<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de sécurité pour gérer l'authentification et la déconnexion.
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et traite les erreurs éventuelles.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     *
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode est vide : Symfony intercepte la déconnexion automatiquement.');
    }
}
