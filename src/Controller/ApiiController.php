<?php

namespace App\Controller;

use Goutte\Client;
use App\Entity\Lieu;
use App\Entity\Legume;
use App\Entity\Glaneur;
use App\Entity\Actualite;
use App\Entity\Evenement;
use App\Entity\Agriculteur;
use App\Entity\Utilisateur;
use App\Form\EvenementType;
use App\Form\GlaneurZoneType;
use App\Entity\EvenementLegume;
use App\Entity\EvenementGlaneur;
use App\Repository\LieuRepository;
use App\Entity\EvenementRecuperateur;
use App\Repository\EvenementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementGlaneurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/api")
 */
class ApiiController extends AbstractController
{
    private $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer($classMetadataFactory)
        ];
        $encoders = [new JsonEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/utilisateur/get", name="utilisateur_get")
     */
    public function utilisateur_get()
    {
        $user = $this->getUser();
        $data = $this->serializer->normalize(
            $user,
            null,
            [
                'groups' => 'fromUtilisateur',
                ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true
            ]
        );
        return new JsonResponse($data);
    }

    /**
     * @Route("/lieu/list", name="lieu_list")
     */
    public function lieu_list(LieuRepository $repository)
    {
        $lieux = $repository->findBrittany();
        $data = $this->serializer->normalize($lieux, null);
        return new JsonResponse($data);
    }

    /**
     * @Route("/legume/list", name="legume_list")
     */
    public function legume_list()
    {
        $legumes = $this->getDoctrine()->getRepository(Legume::class)->findAll();
        $data = $this->serializer->normalize($legumes, null);
        return new JsonResponse($data);
    }

    /**
     * @Route("/evenement/list", name="evenement_list")
     */
    public function evenement_list(SerializerInterface $serializer, EvenementRepository $evenementRepository)
    {
        if (
            $this->getUser()->getRoles()[0] == 'ROLE_ADMIN' ||
            $this->getUser()->getRoles()[0] == 'ROLE_AMBASSADEUR'
        ) {
            $groups = ['fromEvenement', 'fromEvenementAdmin'];
        } else {
            $groups = ['fromEvenement'];
        }

        $evenements = $evenementRepository->findByDistance($this->getUser());
        $data = $this->serializer->normalize(
            $evenements,
            null,
            [
                'groups' => $groups,
                ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true
            ]
        );
        return new JsonResponse($data);
    }

    /**
     * @Route("/evenement/get/{id<\d+>}", name="evenement_get")
     */
    public function evenement_get(int $id, SerializerInterface $serializer)
    {
        if (
            $this->getUser()->getRoles()[0] == 'ROLE_ADMIN' ||
            $this->getUser()->getRoles()[0] == 'ROLE_AMBASSADEUR'
        ) {
            $groups = ['fromEvenement', 'fromEvenementAdmin'];
        } else {
            $groups = ['fromEvenement'];
        }

        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $data = $this->serializer->normalize(
            $evenement,
            null,
            [
                'groups' => $groups,
                ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true
            ]
        );
        return new JsonResponse($data);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AMBASSADEUR')")
     * @Route("/agriculteur/list", name="agriculteur_list")
     */
    public function agriculteur_list(SerializerInterface $serializer)
    {
        $agriculteurs = $this->getDoctrine()->getRepository(Agriculteur::class)->findAll();
        $data = $this->serializer->normalize(
            $agriculteurs,
            null,
            [
                'groups' => ['fromEvenement'],
                ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true
            ]
        );
        return new JsonResponse($data);
    }

    /**
     * @Route("/actualite/list", name="actualite_list")
     */
    public function actualite_list(SerializerInterface $serializer)
    {
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $data = $this->serializer->normalize($actualites, null);
        return new JsonResponse($data);
    }

    /**
     * @Route("/actualite/get/{id<\d+>}", name="actualite_get")
     */
    public function actualite_get(int $id, SerializerInterface $serializer)
    {
        $actualite = $this->getDoctrine()->getRepository(Actualite::class)->find($id);
        $data = $this->serializer->normalize($actualite, null);
        return new JsonResponse($data);
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

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_GLANEUR')")
     * @Route("/evenementGlaneur/new", name="evenementGlaneur_new")
     */
    public function evenementGlaneur_new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $evenementGlaneur = new EvenementGlaneur();
        $evenementGlaneur->setGlaneur($this->getUser())
            ->setEvenement($evenementRepository->find($data['evenement']['id']))
            ->setEffectif($data['effectif']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenementGlaneur);
        $entityManager->flush();

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_GLANEUR')")
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

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_GLANEUR')")
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

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/new", name="evenementRecuperateur_new")
     */
    public function evenementRecuperateur_new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = new EvenementRecuperateur();
            $evenementRecuperateur->setRecuperateur($this->getUser())
                ->setEvenement($evenementRepository->find($data[$i]['evenement']['id']))
                ->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id']))
                ->setVolume($data[$i]['volume']);
            $entityManager->persist($evenementRecuperateur);
        }
        $entityManager->flush();

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/edit", name="evenementRecuperateur_edit")
     */
    public function evenementRecuperateur_edit(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = $this->getDoctrine()->getRepository(EvenementRecuperateur::class)
                ->findOneBy([
                    'evenement' => $evenementRepository->find($data[$i]['evenement']['id']),
                    'recuperateur' => $this->getUser(),
                    'legume' => $this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id'])
                ]);
            $evenementRecuperateur->setVolume($data[$i]['volume']);
            $entityManager->persist($evenementRecuperateur);
        }
        $entityManager->flush();

        return new JsonResponse('', 204);
    }
    /**
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/delete", name="evenementRecuperateur_delete")
     */
    public function evenementRecuperateur_delete(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = $this->getDoctrine()->getRepository(EvenementRecuperateur::class)
                ->findOneBy([
                    'evenement' => $evenementRepository->find($data[$i]['evenement']['id']),
                    'recuperateur' => $this->getUser(),
                    'legume' => $this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id'])
                ]);
            $entityManager->remove($evenementRecuperateur);
        }
        $entityManager->flush();

        return new JsonResponse('', 204);
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

        return new JsonResponse('', 204);
    }

    /**
     * @Route("/utilisateur/edit/pass", name="utilisateur_edit_pass")
     */
    public function utilisateur_edit_pass(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $password = $passwordEncoder->encodePassword($user, $data['password']);
        $user->setPassword($password);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGRICULTEUR') or is_granted('ROLE_AMBASSADEUR')")
     * @Route("/evenement/new", name="evenement_new")
     */
    public function evenement_new(Request $request,  SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);
        $evenement = new Evenement();

        $evenement = $serializer->deserialize(
            json_encode($data),
            Evenement::class,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'date',
                //'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenementLegumes = [];
        for ($i = 0; $i < count($data['evenementLegumes']); $i++) {
            $evenement_legume = new EvenementLegume();
            $evenement_legume->setVolume($data['evenementLegumes'][$i]['volume']);
            $evenement_legume->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data['evenementLegumes'][$i]['legume']['id']));
            //$evenement->addEvenementLegume($evenement_legume);
            $evenementLegumes[] = $evenement_legume;
        }
        $evenement->setEvenementLegumes($evenementLegumes);

        $evenement->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_AMBASSADEUR')) {
            $evenement->setAgriculteur($this->getDoctrine()->getRepository(Agriculteur::class)->find($data['agriculteur']));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();

        return new JsonResponse('', 204);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGRICULTEUR') or is_granted('ROLE_AMBASSADEUR')")
     * @Route("/evenement/edit/{id<\d+>}", name="evenement_edit")
     */
    public function evenement_edit(Request $request,  SerializerInterface $serializer, int $id)
    {
        $data = json_decode($request->getContent(), true);
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);

        $temp_evenement = $serializer->deserialize(
            json_encode($data),
            Evenement::class,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                //'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenementLegumes = [];
        for ($i = 0; $i < count($data['evenementLegumes']); $i++) {
            $evenement_legume = new EvenementLegume();
            $evenement_legume->setVolume($data['evenementLegumes'][$i]['volume']);
            $evenement_legume->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data['evenementLegumes'][$i]['legume']['id']));
            //$evenement->addEvenementLegume($evenement_legume);
            $evenementLegumes[] = $evenement_legume;
        }
        $evenement->setEvenementLegumes($evenementLegumes);

        $evenement->setDeroulements($temp_evenement->getDeroulements())
            ->setRendezvouses($temp_evenement->getRendezvouses());

        $evenement->setDate(new \DateTime($data['date']))
            ->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_AMBASSADEUR')) {
            $evenement->setAgriculteur($this->getDoctrine()->getRepository(Agriculteur::class)->find($data['agriculteur']));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();

        return new JsonResponse('', 204);
    }
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/scraper", name="scraper")
     */
    public function scraper(SerializerInterface $serializer)
    {
        // $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        // foreach ($actualites as $actualite) {
        //     $entityManager->remove($actualite);
        // }
        // $entityManager->flush();
        $client = new Client();
        $crawler = $client->request('GET', 'http://glanage-solidaire.fr');
        $crawler->filter('.actualites li')->each(function ($node) {
            $lien = $node->filter('a')->last()->attr('href');
            $client2 = new Client();
            $crawler2 = $client2->request('GET', $lien);
            if (!$this->getDoctrine()->getRepository(Actualite::class)->findOneBy(array('titre' => $crawler2->filter('.titre')->text()))) {
                $actualite = new Actualite();
                $actualite->setTitre($crawler2->filter('.titre')->text())
                    ->setContenu($crawler2->filter('.contenu')->text())
                    ->setDate(date_create_from_format('d/m/y', $crawler2->filter('.date')->text()))
                    ->setLien($lien);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($actualite);
                $entityManager->flush();
                $crawler2->filter('.galerie img')->each(function ($node2) {
                    $actualite = $this->getDoctrine()->getRepository(Actualite::class)->findOneBy(array(), array('id' => 'DESC'), 1, 0);
                    $img = file_get_contents($node2->attr('src'));
                    $path = "image" . "/" . basename($node2->attr('src'));
                    file_exists($path) ?: file_put_contents($path, $img);
                    $actualite->addImage($path);
                });
            }
        });
        //$actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        //$data = $serializer->serialize($actualites, 'json');
        //$response = new Response($data);
        
        return new JsonResponse('', 204);
    }
}
