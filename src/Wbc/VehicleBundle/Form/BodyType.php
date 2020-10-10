<?php

declare(strict_types=1);

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BodyType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BodyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'             => 'Vehicle Body Type',
            'choices'           => self::getBodyTypes(),
            'placeholder'       => '',
            'choices_as_values' => true,
            'choice_label'      => function ($value, $key, $index) {
                if ($value) {
                    return $value;
                }
            }, ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wbc_vehicle_body_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return array
     */
    public static function getBodyTypes(): array
    {
        return [
            'SUV',
            'COUPE',
            'CONVERTIBLE',
            'HATCHBACK',
            'SEDAN',
            'LIFTBACK',
            'MPV',
            'HALF PANEL VAN',
            'PANEL VAN',
            'PASSENGER VAN',
            'STATION WAGON',
            'PICK UP DOUBLE CAB',
            'PICK UP SINGLE CAB',
            'MINIVAN',
            'SUT',
            'PICKUP CREW CAB',
            'HARD TOP',
            'PICKUP EXTENDED CAB LONG BED',
            'PICKUP EXTENDED CAB SHORT BED',
            'PICKUP EXTENDED CREW CAB',
            'PICKUP SINGLE CAB',
            'PICKUP SINGLE CAB LONG BED',
            'PICKUP EXTENDED CAB',
            'EXTENDED MINIVAN',
            'SHORT BED CREW CAB PICKUP',
            'SHORT BED DOUBLE CAB PICKUP',
            'LONG BED REGULAR CAB PICKUP',
            'SHORT BED REGULAR CAB PICKUP',
            'VAN',
            'CROSSOVER',
            'CARGO VAN',
            'PICK UP',
            'SEMI-PANEL VAN',
            'WINDOW VAN',
            'MINI BUS',
            'EXTENDED CARGO VAN',
            'EXTENDED PASSENGER VAN',
            'PICK UP SUPER CAB',
            'CREW CAB PICKUP',
            'BUS',
            'EXTENDED CAB',
            'LONG BED CREW CAB PICKUP',
            'REGULAR CAB',
            'EXTENDED CAB PICKUP',
            'EXTRA LONG MINIVAN',
            'LONG MINIVAN/POP-UP ROOF',
            'LONG MINIVAN',
            'PICKUP CREW CAB SHORT BED',
            'PICKUP CREW CAB LONG BED',
            'HARD TOP CONVERTIBLE',
            'T-TOP COUPE',
            'PICK UP EXTENDED CAB',
            'PICK UP LWB DOUBLE CAB',
            'SOFT TOP',
            'PANEL VAN LWB',
            'PANEL VAN SWB',
            'MINI BUS LWB WIDE SEMI H/ROOF',
            'PANEL VAN HIGH ROOF',
            'WIDE CAB CHASSIS',
            'PICK UP CREW CAB',
            'PICKUP EXTENDED CREW CAB SHORT BED',
            'VAN L/ROOF',
            'SHORT BED EXTENDED CAB PICKUP',
            'LONG BED EXTENDED CAB PICKUP',
            'STANDARD BED EXTENDED CAB PICKUP',
            'STANDARD BED REGULAR CAB PICKUP',
            'PICKUP SINGLE CAB SHORT BED',
            'SOFT TOP CONVERTIBLE',
            'REGULAR CAB PICKUP',
            'TRUCK SINGLE CAB',
        ];
    }
}
