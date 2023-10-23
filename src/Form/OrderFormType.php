<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Transporteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $utilisateur = $options['utilisateur'];
        $builder
            ->add('adresses', EntityType::class, [
                'class' => Adresse::class, 
                'label' => false, 
                'required' => true, 
                'multiple' => false, 
                'choices' => $utilisateur->getAdresses(),
                'expanded' => true
            ])
            ->add('transporteur', EntityType::class, [
                'class' => Transporteur::class,
                'label' => false, 
                'required' => true, 
                'multiple' => false, 
                'expanded' => true
            ])
            ->add('paiement', ChoiceType::class, [
                'choices' => [
                    'Payer avec Paypal' => 'paypal', 
                    'Payer avec Stripe' => 'stripe'
                ],
                'label' => false, 
                'required' => true, 
                'multiple' => false, 
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'utilisateur' => []
        ]);
    }
}
