<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// ====================================================== //
// ====================== ROUTES ======================== //
// ====================================================== //
class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil', methods: ['GET'])]

// ====================================================== //
// ====================== METHODES ====================== //
// ====================================================== //

// Affiche le profil de l'utilisateur connecté.

    public function index(): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {throw $this->createAccessDeniedException();}
        return $this->render('profil/index.html.twig', ['utilisateur' => $utilisateur,]);
    }

    #[Route('/profil/modifier', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    
// Permet à l'utilisateur de modifier son profil.
    
    public function edit(Request $request,EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {throw $this->createAccessDeniedException();}
        $form = $this->createForm(ProfilType::class,$utilisateur);
        $form->handleRequest($request);

// Modifie le mot de passe uniquement si un nouveau mot de passe est saisi.

        if ($form->isSubmitted() && $form->isValid()) {$plainPassword = $form->get('plain_password')->getData();
            if (!empty($plainPassword)) {$hashedPassword = password_hash($plainPassword,PASSWORD_BCRYPT);

                $utilisateur->setUtilisateurPassword($hashedPassword);
            }
            $entityManager->flush();$this->addFlash('success','Votre profil a été modifié avec succès.');

        return $this->redirectToRoute('app_profil',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/edit.html.twig', ['utilisateur' => $utilisateur,'form' => $form,]);
    }
}