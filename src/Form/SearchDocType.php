<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;

class SearchDocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots', TextType::class,[
            	'label' => 'Rechercher un document',
            	'required' => false,
            	'constraints' => [
            		new Regex([
            			'match' => false,
            			'pattern' => "/%/",
            			'message' => "Votre saisie ne doit pas contenir le caractère %",
            		])
            	],
            ])
        ;
    }

}
