<?php
/**
 * Created by PhpStorm.
 * User: eliasmanzaneda
 * Date: 21/06/18
 * Time: 09:38
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

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarPath', FileType::class, array('data_class' => null))
            ->add('changeavatar', SubmitType::class, array('label' => 'Cambiar avatar',
                'attr' => array(
                    'class' => 'btn btn-lg btn-primary btn-block signinbtn custombutton'
                )))
        ;
        // ->add('avatarPath', FileType::class, array('data_class' => null, 'required' => true))
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}