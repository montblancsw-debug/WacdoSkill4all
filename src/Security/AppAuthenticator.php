<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{

    use TargetPathTrait;

// ====================================================== //
// ===================== CONSTANTES. ==================== //
// ====================================================== //

    // Définit la route utilisée pour afficher le formulaire de connexion.
    public const LOGIN_ROUTE = 'app_login';

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

    // Initialise le générateur d'URL utilisé pour les redirections.
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    // Prépare les informations utilisées pour l'authentification.
    public function authenticate(Request $request): Passport
    {

    // Récupère l'adresse email saisie dans le formulaire.
        $email = trim($request->getPayload()->getString('email'));

        // Mémorise l'email pour le réafficher en cas d'erreur.
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME,$email);

        // Crée les informations nécessaires pour vérifier la connexion.
        return new Passport(new UserBadge($email),new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate',$request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    // Gère la redirection après une connexion réussie.
    public function onAuthenticationSuccess(Request $request,TokenInterface $token,string $firewallName): ?Response
    {
        // Ajoute un message de confirmation après la connexion.
        $session = $request->getSession();

        if ($session instanceof FlashBagAwareSessionInterface)
        {
            $session->getFlashBag()->add('success','Vous êtes bien connecté.');
        }
        // Récupère les rôles de l'utilisateur connecté.
        $roles = $token->getRoleNames();

        // Vérifie si l'utilisateur possède un rôle donnant accès au back-office.
        $hasBackendAccess =in_array('ROLE_ADMIN', $roles, true)|| in_array('ROLE_PREPARATEUR', $roles, true)|| in_array('ROLE_ACCUEIL', $roles, true);

        // Récupère la page demandée avant la connexion.
        $targetPath = $this->getTargetPath($request->getSession(),$firewallName);
        if ($targetPath)
        {
            // Ignore une ancienne redirection vers l'administration.
            if (str_starts_with($targetPath, '/admin'))
            {
                $this->removeTargetPath($request->getSession(),$firewallName);
            } else {
                return new RedirectResponse($targetPath);
            }
        }

        // Redirige les utilisateurs du back-office vers le tableau de bord.
        if ($hasBackendAccess)
        {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        }
        // Redirige les clients vers l'accueil du site.
        return new RedirectResponse($this->urlGenerator->generate('app_front_home'));
    }

    // Retourne l'adresse de la page de connexion.
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}