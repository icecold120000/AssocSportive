<?php

namespace App\Form;

use App\Entity\Classe;
use App\Repository\ClasseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;

class FilterEleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('classe', EntityType::class,[
                'label' => 'Classe',
                'class' => Classe::class,
                'query_builder' => function (ClasseRepository $er) {
                    return $er->createQueryBuilder('cl')
                        ->orderBy('cl.id');
                },
                'choice_label' => 'libelle',
                'choice_value' => function (?Classe $classe) {
                    return $classe ? $classe->getId() : '';
                },
                'required' => false,
            ])
            ->add('genreEleve', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'H',
                    'Femme' => 'F',
                ],
                'required' => false,
            ])
            ->add('archiveEleve', ChoiceType::class, [
                'label' => 'Archive',
                'choices' => [
                    'Non Archivé' => '0',
                    'Archivé' => 1,
                ],
                'required' => false,
            ])
        ;
    }

}