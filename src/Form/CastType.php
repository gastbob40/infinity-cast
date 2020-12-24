<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\Cast;
use App\Entity\WebHook;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('association', EntityType::class, [
                'class' => Association::class,
                'choices' => $options['associations'],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['rows' => '8'],
                'help' => 'Description présente dans le webhook'
            ])
            ->add('image', TextType::class, [
                'required' => false,
                'help' => 'Image non obligatoire mise en dessous de l\'annonce.'
            ])
            ->add('webhooks', EntityType::class, [
                'class' => WebHook::class,
                'choices' => $options['webhooks'],
                'required' => true,
                'expanded' => true,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cast::class,
            'associations' => [],
            'webhooks' => []
        ]);
    }
}
