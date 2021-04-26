<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Eleve;
use App\Repository\EleveRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class,[
                'label' => 'L\'email de l\'utilisateur',
                'required' => true,
            ])
            ->add('roles', CollectionType::class, [
                'label' => 'Le rôle de l\'utilisateur',
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'choices'  => [
                        'Utilisateur'=> User::ROLE_USER ,
                        'Admin' => User::ROLE_ADMIN,
                        'Eleve' => User::ROLE_ELEVE,
                        'Enseignant' => User::ROLE_ENSEIGNANT,
                        'Comptable' => User::ROLE_COMPTABLE,
                    ],
                    'required' => true,
                ],
            ])
            ->add('password', PasswordType::class,[
                'label' => 'Le mot de passe de l\'utilisateur',
                'required' => true,
            ])
            ->add('nomUser', TextType::class,[
                'label' => 'Le nom de l\'utilisateur',
                'required' => true,
            ])
            ->add('prenomUser', TextType::class,[
                'label' => 'Le prénom de l\'utilisateur',
                'required' => true,
            ])
            ->add('eleve', EntityType::class,[
                'label' => 'L\'élève auquel l\'utilisateur appartient',
                'class' => Eleve::class,
                'query_builder' => function (EleveRepository $er) {
                    return $er->createQueryBuilder('el');
                },
                'choice_label' => 'nomEleve',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
