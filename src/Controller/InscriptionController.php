<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Admin;
use App\Entity\Glaneur;
use App\Entity\Agriculteur;
use App\Entity\Utilisateur;
use App\Entity\Recuperateur;
use App\Entity\ResetPassword;
use App\Entity\ValidateEmail;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class InscriptionController extends AbstractController
{

    /**
     * @Route("register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {

        $data = json_decode($request->getContent(), true);

        $data['password'] = 'root';

        $utilisateur = new Glaneur();

        $utilisateur->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setUsername($data['firstname'] . ' ' . $data['lastname'])
            ->setEmail($data['email'])
            ->setPerimetre(50)
            ->setLieu($this->getDoctrine()->getRepository(Lieu::class)->findOneBy(['commune' => 'Rennes']));
        $passwordCrypte = $encoder->encodePassword($utilisateur, $data['password']);
        $utilisateur->setPassword($passwordCrypte)
            ->setEnabled(False);
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->processSendingEmail($request, $mailer, $entityManager, $utilisateur);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AMBASSADEUR')")
     * @Route("api/utilisateur/new", name="utilisateur_new")
     */
    public function utilisateur_new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {

        $data = json_decode($request->getContent(), true);

        $data['password'] = 'root';
        
        $class = 'App\Entity\\' . $data['type'];
        $utilisateur = new $class();

        $utilisateur->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setPerimetre(50)
            ->setLieu($this->getDoctrine()->getRepository(Lieu::class)->findOneBy(['commune' => 'Rennes']));
        $passwordCrypte = $encoder->encodePassword($utilisateur, $data['password']);
        $utilisateur->setPassword($passwordCrypte)
            ->setEnabled(False);
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->processSendingEmail($request, $mailer, $entityManager, $utilisateur);
    }

    /**
     * @Route("/validate-email", name="app_validate_email")
     */
    public function validate(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder ): Response
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        $token = $data['token'];

        $validateEmail = $this->getDoctrine()->getRepository(ValidateEmail::class)->findOneBy(['utilisateur' => $utilisateur]);

        $ExpiresAt = $validateEmail->getRequestedAt()->modify('+3600 second');
        if ($token === $validateEmail->getToken() && $ExpiresAt > new \DateTime()) {
 
            $passwordCrypte = $encoder->encodePassword($utilisateur, $data['password']);
            $utilisateur->setPassword($passwordCrypte)
                ->setEnabled(True);
            $entityManager->persist($utilisateur);
            $entityManager->remove($validateEmail);
            $entityManager->flush();

            return new Response('', 204);
        } else {
            return new Response('Code invalide ou expiré.', 403);
        }
    }

    /**
     * @Route("/send-token", name="app_send_token")
     */
    public function processSendingEmail(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, Utilisateur $user = null): Response
    {
        if (!$user) {
            $data = json_decode($request->getContent(), true);
            $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);

            $validateEmail = $this->getDoctrine()->getRepository(ValidateEmail::class)->findOneBy(['utilisateur' => $user]);
            $entityManager->remove($validateEmail);
        }
        $token = strtoupper(bin2hex(random_bytes(4)));

        $validateEmail = new ValidateEmail();
        $validateEmail->setUtilisateur($user)
            ->setToken($token)
            ->setRequestedAt(new \DateTime());
        $entityManager->persist($validateEmail);
        $entityManager->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('harrycodeur2020@gmail.com', 'Harry Codeur'))
            ->to($user->getEmail())
            ->subject('Validation de votre compte !')
            ->htmlTemplate('validate_account_email.html.twig')
            ->context([
                'token' => $token,
                'tokenLifetime' => 3600,
            ]);

        $mailer->send($email);

        $response = new Response('', 204);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/email_check", name="email_check")
     */
    public function email_check(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);

        if ($utilisateur) {
            if ($utilisateur->isEnabled()) {
                $response = new Response('', 204);
            } else {
                $response = new Response('Unverified email.', 401);
            }
        } else {
            $response = new Response('Unexisting email.', 401);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/email_check_token", name="email_check_token")
     */
    public function emailCheckToken(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        $token = $data['token'];
        $validateEmail = $this->getDoctrine()->getRepository(ValidateEmail::class)->findOneBy(['utilisateur' => $user]);

        $ExpiresAt = $validateEmail->getRequestedAt()->modify('+3600 second');
        if ($token === $validateEmail->getToken() && $ExpiresAt > new \DateTime()) {
            return new Response('', 204);
        } else {
            return new Response('Code invalide ou expiré.', 403);
        }
    }

    /**
     * @Route("/password_check_token", name="password_check_token")
     */
    public function passwordCheckToken(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder ): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        $token = $data['token'];

        $resetPassword = $this->getDoctrine()->getRepository(ResetPassword::class)->findOneBy(['utilisateur' => $user]);

        $ExpiresAt = $resetPassword->getRequestedAt()->modify('+3600 second');
        if ($token === $resetPassword->getToken() && $ExpiresAt > new \DateTime()) {
            return new Response('', 204);
        } else {
            return new Response('Code invalide ou expiré.', 403);
        }
    }
}
