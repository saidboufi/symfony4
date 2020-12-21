<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'article"
            ])
            ->add('category', EntityType::class, [
                            'class' => Category::class,
                            'choice_label' => 'title',
                            'attr' => [
                                'class' => 'select-category'
                                ],
                            'multiple' => true,
                            'expanded' => false,
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('c')
                                        ->orderBy('c.title','DESC');
                            }
                            ])
            ->add('content', TextareaType::class, [
                'required' => true
            ])
            ->add('image');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}