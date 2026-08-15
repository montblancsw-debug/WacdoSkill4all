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
    public const LOGIN_ROUTE = 'app_login';
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    // Prépare les informations utilisées pour l'authentification.
    public function authenticate(Request $request): Passport
    {
        // Récupère l'adresse email saisie.
        $email = trim(
            $request->getPayload()->getString('email')
        );

        // Mémorise l'email pour le réafficher en cas d'erreur.
        $request->getSession()->set(
            SecurityRequestAttributes::LAST_USERNAME,
            $email
        );

        // Construit le passeport de connexion Symfony.
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials(
                $request->getPayload()->getString('password')
            ),
            [
                new CsrfTokenBadge(
                    'authenticate',
                    $request->getPayload()->getString('_csrf_token')
                ),
                new RememberMeBadge(),
            ]
        );
    }

    // Gère la redirection après une connexion réussie.
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // Retourne vers la page initialement demandée.
        if (
            $targetPath = $this->getTargetPath(
                $request->getSession(),
                $firewallName
            )
        ) {
            return new RedirectResponse(
                $targetPath
            );
        }

        // Récupère la session courante.
        $session = $request->getSession();

        // Vérifie que la session possède un FlashBag.
        if ($session instanceof FlashBagAwareSessionInterface) {

            // Ajoute un message après connexion.
            $session->getFlashBag()->add(
                'success',
                'Vous êtes bien connecté.'
            );
        }

        // Redirige vers le Dashboard Wacdo.
        return new RedirectResponse(
            $this->urlGenerator->generate(
                'app_admin_dashboard'
            )
        );
    }

    // Retourne l'adresse du formulaire de connexion.
    protected function getLoginUrl(
        Request $request
    ): string {
        return $this->urlGenerator->generate(
            self::LOGIN_ROUTE
        );
    }
}