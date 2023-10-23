<?php 
    namespace App\Service;

    use Symfony\Bridge\Twig\Mime\TemplatedEmail;
    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Address;
   

    class MailerService {

        public function __construct(private readonly MailerInterface $mailer){}

        /**
         * @throws TransportExceptionInterface
         */

        public function send(
            string $to, 
            string $subject, 
            string $templateTwig, 
            array $context): void 
        {
            $email = (new TemplatedEmail()) 
                ->from(new Address('noreply@atelier-caiman.fr', 'Atelier Caïman'))
                ->to($to) 
                ->subject($subject)
                ->htmlTemplate("mail/$templateTwig")
                ->context($context);

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $transportException){
                /** @var TransportExceptionInterface $transportException */
                throw $transportException;
            }
        }
    }