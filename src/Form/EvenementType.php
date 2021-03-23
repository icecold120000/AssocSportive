<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\TypeEvenement;
use App\Entity\CategorieEleve;
use App\Entity\Sport;
use App\Repository\TypeEvenementRepository;
use App\Repository\SportRepository;
use App\Repository\CategorieEleveRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $evenement = new Evenement();
        $builder
            ->add('nomEvenement', TextType::class,[
                'label' => 'Le nom de l\'événement',
                'required' => false,
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'La date de début de l\'événement',
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy kk:mm:ss',
                'required' => false,
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'La date de fin de l\'événement',
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy kk:mm:ss',
                'required' => false,
            ])
            ->add('lieuEvenement', TextType::class,[
                'label' => 'Le lieu de l\'événement',
                'required' => false,
            ])
            ->add('coutEvenement', TextType::class,[
                'label' => 'Le coût de l\'événement',
                'required' => false,
            ])
            ->add('descripEvenement', TextType::class,[
                'label' => 'La description de l\'événement',
                'required' => false,
            ])
            ->add('nbPlace', TextType::class, [
                'label' => 'Le nombre de places maximum de l\'événement',
                'required' => false,
            ])
            ->add('imgEvent', FileType::class, [
                'label' => 'L\'image de l\'événement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez selectionner un fichier png/jpeg/jpg',
                        'maxSizeMessage' => 'Veuillez transferer un fichier ayant pour taille maximum de {{limit}}',
                    ])
                ]
            ])
            ->add('vigEvent', FileType::class, [
                'label' => 'La vignette de l\'événement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez selectionner un fichier png/jpeg/jpg',
                        'maxSizeMessage' => 'Veuillez transferer un fichier ayant pour taille maximum de {{limit}}',
                    ])
                ]
            ])
            ->add('type', EntityType::class,[
                'label' => 'Le type d\'événement auquel il est attaché',
                'class' => TypeEvenement::class,
                'query_builder' => function (TypeEvenementRepository $er) {
                    return $er->createQueryBuilder('te')
                        ->orderBy('te.id', 'ASC');
                },
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('sport', EntityType::class,[
                'label' => 'Le sport pratiqué durant l\'événement',
                'class' => Sport::class,
                'query_builder' => function (SportRepository $er) {
                    return $er->createQueryBuilder('sp')
                        ->orderBy('sp.id', 'ASC');
                },
                'choice_label' => 'nomSport',
                'required' => false,
            ])
            ->add('categorieEleve', EntityType::class,[
                'label' => 'La catégorie d\'élève qui peut participer',
                'class' => CategorieEleve::class,
                'query_builder' => function (CategorieEleveRepository $er) {
                    return $er->createQueryBuilder('ce')
                        ->orderBy('ce.id');
                },
                'choice_label' => 'libelleCategorie',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
