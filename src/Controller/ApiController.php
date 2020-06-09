<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Glaneur;
use App\Entity\Actualite;
use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Form\GlaneurZoneType;
use App\Entity\EvenementGlaneur;
use App\Repository\EvenementGlaneurRepository;
use App\Repository\LieuRepository;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/utilisateur/get", name="utilisateur_get")
     */
    public function utilisateur_get(SerializerInterface $serializer)
    {
        $user = $this->getUser();
        if ($user->getType() == 'glaneur') {
            $data = $serializer->serialize(
                $user,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'username',
                    'type',
                    'email',
                    'lastname',
                    'firstname',
                    'phone',
                    'perimetre',
                    'lieu',
                    'evenementGlaneurs' => ['effectif', 'evenement' => ['id']]
                ]]
            );
        } else {
            $data = $serializer->serialize(
                $user,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'username',
                    'type',
                    'email',
                    'lastname',
                    'firstname',
                    'phone',
                    'perimetre',
                    'lieu'
                ]]
            );
        }
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/lieu/list", name="lieu_list")
     */
    public function lieu_list(SerializerInterface $serializer, LieuRepository $lieuRepository)
    {
        $lieux = $lieuRepository->findBrittany();
        $data = $serializer->serialize($lieux, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/evenement/list", name="evenement_list")
     */
    public function evenement_list(SerializerInterface $serializer, EvenementRepository $evenementRepository)
    {
        $evenements = $evenementRepository->findByDistance($this->getUser());
        //$evenements = $evenementRepository->findAll();
        $data = $serializer->serialize(
            $evenements,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'date',
                'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'agriculteur' => ['username'],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/evenement/get/{id<\d+>}", name="evenement_get")
     */
    public function evenement_get(int $id, SerializerInterface $serializer)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $data = $serializer->serialize(
            $evenement,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'date',
                'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'agriculteur' => ['username'],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/actualite/list", name="actualite_list")
     */
    public function actualite_list(SerializerInterface $serializer)
    {
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $data = $serializer->serialize($actualites, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/actualite/get/{id<\d+>}", name="actualite_get")
     */
    public function actualite_get(int $id, SerializerInterface $serializer)
    {
        $actualite = $this->getDoctrine()->getRepository(Actualite::class)->find($id);
        $data = $serializer->serialize($actualite, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/utilisateur/update/zone", name="utilisateur_update_zone")
     */
    public function utilisateurUpdateZone(Request $request): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $lieu = $this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']);
        $user->setLieu($lieu);
        $user->setPerimetre($data['perimetre']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/new", name="evenementGlaneur_new")
     */
    public function evenementGlaneur_new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $evenementGlaneur = new EvenementGlaneur();
        $evenementGlaneur->setGlaneur($this->getUser())
            ->setEvenement($evenementRepository->find($data['evenement']['id']))
            ->setEffectif($data['effectif']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/edit", name="evenementGlaneur_edit")
     */
    public function evenementGlaneur_edit(Request $request, EvenementRepository $evenementRepository, EvenementGlaneurRepository $evenementGlaneurRepository): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $evenement = $evenementRepository->find($data['evenement']['id']);
        $evenementGlaneur = $evenementGlaneurRepository->findByEvenementGlaneur($evenement, $user);
        $evenementGlaneur->setEffectif($data['effectif']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/delete", name="evenementGlaneur_delete")
     */
    public function evenementGlaneur_delete(Request $request, EvenementRepository $evenementRepository, EvenementGlaneurRepository $evenementGlaneurRepository): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $evenement = $evenementRepository->find($data['evenement']['id']);
        $evenementGlaneur = $evenementGlaneurRepository->findByEvenementGlaneur($evenement, $user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/utilisateur/edit/infos", name="utilisateur_edit_infos")
     */
    public function utilisateur_edit_infos(Request $request): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $user->setLastname($data['lastname'])
            ->setFirstname($data['firstname'])
            ->setPhone($data['phone']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/utilisateur/edit/pass", name="utilisateur_edit_pass")
     */
    public function utilisateur_edit_pass(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        
        $password = $passwordEncoder->encodePassword($user,$data['password']);
        $user->setPassword($password);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}