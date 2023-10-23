<?php 
    namespace App\Security;

    use App\Entity\Utilisateur;
    use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
    use Symfony\Component\Security\Core\User\UserCheckerInterface;
    use Symfony\Component\Security\Core\User\UserInterface;

    class UserConfirmation implements UserCheckerInterface
    {
        public function checkPreAuth(UserInterface $user): void
        {
            if (!$user instanceof Utilisateur) {
                return;
            }
        }

        public function checkPostAuth(UserInterface $user): void
        {
            if (!$user instanceof Utilisateur) {
                return;
            }

            if (!$user->isIsVerified()) {
                // the message passed to this exception is meant to be displayed to the user
                throw new CustomUserMessageAccountStatusException("Votre compte n'est pas activé. 
                Veuillez cliquer sur le lien reçu par email avant le {$user->getTokenRegistrationLifeTime()->format('d/m/y à H\hi')}.");
            }

        }
    }