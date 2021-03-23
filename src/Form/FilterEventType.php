<?php

namespace App\Form;

use App\Entity\CategorieEleve;
use App\Entity\Sport;
use App\Entity\TypeEvenement;
use App\Repository\CategorieEleveRepository;
use App\Repository\SportRepository;
use App\Repository\TypeEvenementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;

class FilterEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', EntityType::class,[
                'label' => 'Type',
                'class' => TypeEvenement::class,
                'query_builder' => function (TypeEvenementRepository $er) {
                    return $er->createQueryBuilder('te')
                        ->orderBy('te.id');
                },
                'choice_label' => 'nom',
                'choice_value' => function (?TypeEvenement $typeEvenement) {
                    return $typeEvenement ? $typeEvenement->getId() : '';
                },
                'required' => false,
            ])
            ->add('categorieEleve', EntityType::class,[
                'label' => 'Catégorie',
                'class' => CategorieEleve::class,
                'query_builder' => function (CategorieEleveRepository $er) {
                    return $er->createQueryBuilder('ce')
                        ->orderBy('ce.id');
                },
                'choice_label' => 'libelleCategorie',
                'choice_value' => function (?CategorieEleve $categorieEleve) {
                    return $categorieEleve ? $categorieEleve->getId() : '';
                },
                'required' => false,
            ])
            ->add('sport', EntityType::class,[
                'label' => 'Sport',
                'class' => Sport::class,
                'query_builder' => function (SportRepository $er) {
                    return $er->createQueryBuilder('sp')
                        ->orderBy('sp.id');
                },
                'choice_label' => 'nomSport',
                'choice_value' => function (?Sport $sport) {
                    return $sport ? $sport->getId() : '';
                },
                'required' => false,
            ])
            ->add('actif', CheckboxType::class,[
                'label' => 'Actif',
                'required' => false,
            ])
        ;
    }

}