<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Isbn extends Constraint
{
    public string $message = 'L\'ISBN "{{ value }}" n\'est pas valide. Il doit être au format ISBN-10 ou ISBN-13.';
}
