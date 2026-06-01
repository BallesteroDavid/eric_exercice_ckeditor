<?php

namespace App\Form;

use App\Entity\ResearchSubject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchSubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre du sujet de recherche',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'rows' => 12,
                    'class' => 'js-ckeditor',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Brouillon' => ResearchSubject::STATUS_DRAFT,
                    'Publié' => ResearchSubject::STATUS_PUBLISHED,
                    'Archivé' => ResearchSubject::STATUS_ARCHIVED,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResearchSubject::class,
        ]);
    }
}