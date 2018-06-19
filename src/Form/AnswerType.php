<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 19/06/18
 * Time: 13:32
 */

namespace App\Form;

use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('_text', TextareaType::class)
            ->add('_question', HiddenType::class)
            ->add('save', SubmitType::class, array('label' => 'Answer question'))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Answer::class,
        ));
    }
}