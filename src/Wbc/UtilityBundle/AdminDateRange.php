<?php
namespace Wbc\UtilityBundle;

/**
 * Class AdminDateRange.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
final class AdminDateRange
{
    public static function getDoctrineOrmDateRange($label = null)
    {
        $now = new \DateTime();
        $options = [
            'field_type' => 'sonata_type_date_range_picker',
            'field_options' => [
                'field_options' => ['format' => 'MMM dd, yyyy'],
            ],
            'start_options' => [
                'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                'dp_default_date' => $now->format('m/d/Y'),
                'format' => 'MMM dd, yyyy',
            ],
            'end_options' => [
                'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                'dp_default_date' => $now->format('m/d/Y'), 'format' => 'MMM dd, yyyy', ],
        ];

        if ($label) {
            $options['label'] = $label;
        }

        return $options;
    }
}
