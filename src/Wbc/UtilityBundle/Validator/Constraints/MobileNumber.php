<?php

namespace Wbc\UtilityBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class MobileNumber.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MobileNumber extends Regex
{
    public $message = 'Mobile number is not valid';
    public $pattern = '/^(0|((\+|00)?971))(50|51|52|53|54|55|56|57|58|59)\d{7}$/';
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
