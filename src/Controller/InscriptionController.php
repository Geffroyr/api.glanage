<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Agriculteur;
use App\Entity\Glaneur;
use App\Entity\Recuperateur;
use App\Entity\Lieu;
use App\Entity\Utilisateur;
use App\Entity\ValidateEmail;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;

/*
 * @Route("/api")
 */

class InscriptionController extends AbstractController
{

    /**
     * @Route("register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {

        $data = json_decode($request->getContent(), true);
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
     * @Route("api/utilisateur/new", name="utilisateur_new")
     */
    public function utilisateur_new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {

        $data = json_decode($request->getContent(), true);

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
    public function validate(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        $token = $data['token'];

        $validateEmail = $this->getDoctrine()->getRepository(ValidateEmail::class)->findOneBy(['utilisateur' => $user]);

        $ExpiresAt = $validateEmail->getRequestedAt()->modify('+3600 second');
        if ($token === $validateEmail->getToken() && $ExpiresAt > new \DateTime()) {
            $user->setEnabled(True);
            $entityManager->persist($user);
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
}
