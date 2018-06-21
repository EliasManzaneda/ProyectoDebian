<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 21/06/18
 * Time: 07:13
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
            ))
            ->add('username', TextType::class, array(
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array(
                ),
                'second_options' => array(
                ),
            ))



        ;
        // ->add('save', SubmitType::class, array('label' => 'Cambiar Datos'))
//  ->add('avatarPath', FileType::class, array('data_class' => null, 'required' => false))




        /*
          ->add('changeprofile', SubmitType::class, array('label' => 'Cambiar datos',
                'attr' => array(
                    'class' => 'btn btn-lg btn-primary btn-block signinbtn custombutton'
                )))
         */
        // ->add('avatarPath', FileType::class, array('data_class' => null, 'required' => false))
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}