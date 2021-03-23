<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\CategorieEleve;
use App\Repository\ClasseRepository;
use App\Repository\CategorieEleveRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eleve = new Eleve();
        $builder
            ->add('nomEleve', TextType::class,[
                'label' => 'Le nom de l\'élève',
                'required' => false,
            ])
            ->add('prenomEleve', TextType::class,[
                'label' => 'Le prénom de l\'élève',
                'required' => false,
            ])
            ->add('imgFile', FileType::class, [
                'label' => 'La photo de l\'élève(png/jpeg/jpg)',
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
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'La date naissance de l\'élève ',
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false,
            ])
            ->add('genreEleve', ChoiceType::class, [
                'label' => 'Le genre de l\'élève',
                'choices' => [
                    'Homme' => 'H',
                    'Femme' => 'F',
                ],
                'required' => false,
            ])
            ->add('archiveEleve', ChoiceType::class, [
                'label' => 'L\'Archivage de l\'élève',
                'choices' => [
                    'Non Archivé' => 0,
                    'Archivé' => 1,
                ],
                'required' => false,
            ])
            ->add('numTelEleve', TextType::class, [
                'label' => 'Le numéro de téléphone de l\'élève',
                'required' => false,
            ])
            ->add('numTelParent', TextType::class, [
                'label' => 'Le numéro de téléphone d\'un parent de l\'élève',
                'required' => false,
            ])
            ->add('classe', EntityType::class,[
                'label' => 'La classe de l\'élève',
                'class' => Classe::class,
                'query_builder' => function (ClasseRepository $er) {
                    return $er->createQueryBuilder('cl')
                        ->orderBy('cl.libelle', 'ASC');
                },
                'choice_label' => 'libelle',
                'required' => false,
            ])
            ->add('categorie', EntityType::class,[
                'label' => 'La catégorie de l\'élève',
                'class' => CategorieEleve::class,
                'query_builder' => function (CategorieEleveRepository $er) {
                    return $er->createQueryBuilder('ca')
                        ->orderBy('ca.libelleCategorie', 'ASC');
                },
                'choice_label' => 'libelleCategorie',
                'required' => false,
            ])
            ->add('autoPrelev', ChoiceType::class, [
                'label' => 'Autorisation de prélèvement',
                'choices' => [
                    'Non' => '0',
                    'Oui' => '1',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
