<?php

namespace App\Enum;

enum EtatExemplaire: string
{
    case DAMAGED = 'DAMAGED';
    case CORRECT = 'CORRECT';
    case NEW = 'NEW';
}
