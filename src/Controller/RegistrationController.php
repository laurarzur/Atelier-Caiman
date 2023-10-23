<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request, UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, 
        MailerService $mailerService, 
        TokenGeneratorInterface $tokenGeneratorInterface
        
        ): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //token 
            $tokenRegistration = $tokenGeneratorInterface->generateToken();
            
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // user token
            $user->setTokenRegistration($tokenRegistration); 

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //mailer send
            $mailerService->send(
                $user->getEmail(),
                "Confirmation de compte",
                "registration_confirmation.html.twig", 
                [
                    'utilisateur' => $user, 
                    'token' => $tokenRegistration, 
                    'lifeTimeToken' => $user->getTokenRegistrationLifeTime()->format('d/m/Y à H\hi')
                ]
            );

            $this->addFlash('success', "Votre compte a bien été créé. Veuillez l'activer en suivant le lien dans l'email qui vous a été envoyé.");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}/{id<\d+>}', name: 'account_verify', methods: ['GET'])]
    public function verify(string $token, Utilisateur $user, EntityManagerInterface $em): Response {

        if($user->getTokenRegistration() !== $token 
        || $user->getTokenRegistration() === null 
        || new DateTime('now') > $user->getTokenRegistrationLifeTime()) {
            throw new AccessDeniedException();
        }

        $user->setIsVerified(true); 
        $user->setTokenRegistration(null); 
        $em->flush(); 

        $this->addFlash('success', 'Votre compte a bien été activé. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
