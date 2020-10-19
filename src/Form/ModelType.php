<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\Color;
use App\Entity\Manufacture;
use App\Entity\Model;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{NumberType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Model form.
 */
class ModelType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('manufacture', EntityType::class, [
                'class' => Manufacture::class,
                'choice_label' => 'name',
            ])
            ->add('price', NumberType::class)
            ->add('colors', EntityType::class, [
                'class' => Color::class,
                'multiple' => true,
                'choice_label' => 'name',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
        ]);
    }
}
