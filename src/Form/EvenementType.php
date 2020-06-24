<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Evenement;
use App\Entity\Agriculteur;
use App\Form\RendezvousType;
use App\Form\DeroulementType;
use App\Form\EvenementLegumeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('agriculteur', EntityType::class, [
                'class' => Agriculteur::class
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class
            ])
            ->add('evenementLegumes', CollectionType::class, [
                'entry_type' => EvenementLegumeType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('deroulements', CollectionType::class, [
                'entry_type' => DeroulementType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('rendezvouses', CollectionType::class, [
                'entry_type' => RendezvousType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
