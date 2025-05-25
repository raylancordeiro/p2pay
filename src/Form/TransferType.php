<?php

namespace App\Form;

use App\Validator\Constraints\UserExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ],
            ])
            ->add('payer', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                    new UserExists(),
                ],
            ])
            ->add('payee', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                    new UserExists(),
                ],
            ]);
    }
}
