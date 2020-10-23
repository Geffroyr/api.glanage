<?php

namespace App\Controller;

use App\Entity\Glaneur;
use App\Entity\Lieu;
use App\Entity\ResetPassword;
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

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset-password", name="app_reset_password")
     */
    public function reset(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);
        $token = $data['token'];
        $password = $encoder->encodePassword($user, $data['password']);

        $resetPassword = $this->getDoctrine()->getRepository(ResetPassword::class)->findOneBy(['utilisateur' => $user]);

        $ExpiresAt = $resetPassword->getRequestedAt()->modify('+3600 second');
        if ($token === $resetPassword->getToken() && $ExpiresAt > new \DateTime()) {
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->remove($resetPassword);
            $entityManager->flush();

            return new Response('', 204);
        } else {
            return new Response('Code invalide ou expiré.', 403);
        }
    }

    /**
     * @Route("/send-reset-token", name="app_send_reset_token")
     */
    public function sendResetToken(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);

        $resetPassword = $this->getDoctrine()->getRepository(ResetPassword::class)->findOneBy(['utilisateur' => $user]);
        if ($resetPassword) {
            $entityManager->remove($resetPassword);
        }

        $token = strtoupper(bin2hex(random_bytes(4)));

        $resetPassword = new ResetPassword();
        $resetPassword->setUtilisateur($user)
            ->setToken($token)
            ->setRequestedAt(new \DateTime());
        $entityManager->persist($resetPassword);
        $entityManager->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('harrycodeur2020@gmail.com', 'Harry Codeur'))
            ->to($user->getEmail())
            ->subject('Reinitialisation de votre mot de passe !')
            ->htmlTemplate('reset_password_email.html.twig')
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
