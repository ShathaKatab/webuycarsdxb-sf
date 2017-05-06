<?php

namespace Wbc\UtilityBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class PhoneNumber.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class PhoneNumber extends Regex
{
    public $message = 'Phone number is not valid';
    public $pattern = '/^(0|((\+|00)?971))(2|3|4|6|7|9|50|52|54|55|56)\d{7}$/';
    public $match = true;

    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\RegexValidator';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return [];
    }
}
