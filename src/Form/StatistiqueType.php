<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Eleve;
use App\Repository\EleveRepository;
use App\Repository\EvenementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StatistiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Annee_scolaire', ChoiceType::class, [
                'label' => 'Année scolaire:',
                'choices' => [
                    '2019/2020' => '2019/2020',
                    '2020/2021' => '2020/2021',
                    '2021/2022' => '2021/2022',
                    '2022/2023' => '2022/2023',
                    '2023/2024' => '2023/2024',
                    '2024/2025' => '2024/2025',
                    '2025/2026' => '2025/2026',
                    '2026/2027' => '2026/2027',
                    '2027/2028' => '2027/2028',
                ],
                'required' => false,
            ])
            ->add('Annee_de_Naissance', EntityType::class,[
                'label' => 'Année de naissance:',
                'class' => Eleve::class,
                'placeholder' => 'Choisir un événement',
                'query_builder' => function (EleveRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->select()
                        ->distinct('Year(e.dateNaissance)')
                        ->orderBy('e.dateNaissance','ASC')
                    ;
                },
                'choice_label' => strtotime('dateNaissance') ,
                'required' => false,
            ])
            ->add('EvenementFilter', EntityType::class,[
                'label' => 'Evénement:',
                'class' => Evenement::class,
                'placeholder' => 'Choisir un événement',
                'query_builder' => function (EvenementRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nomEvenement', 'ASC');
                },
                'choice_label' => 'nomEvenement',
                'required' => false,
            ])
            ->add('EleveFiltre', EntityType::class,[
                'label' => 'Elève:',
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
            // Configure your form options here
        ]);
    }
}
