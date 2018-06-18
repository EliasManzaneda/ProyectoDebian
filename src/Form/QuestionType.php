<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 01/05/18
 * Time: 20:11
 */

namespace App\Form;

use App\Entity\User;
use App\Entity\Question;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;




class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('_title', TextType::class)
            ->add('_text', TextareaType::class)
            ->add('_tags', EntityType::class, array(
                'class'        => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('save', SubmitType::class, array('label' => 'Ask Question'))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Question::class,
        ));
    }


}