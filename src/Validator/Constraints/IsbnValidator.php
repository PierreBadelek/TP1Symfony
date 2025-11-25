<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsbnValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Isbn) {
            throw new UnexpectedTypeException($constraint, Isbn::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // Nettoyer l'ISBN (enlever tirets et espaces)
        $isbn = preg_replace('/[\s-]/', '', $value);

        // Valider ISBN-10 ou ISBN-13
        if (!$this->isValidIsbn10($isbn) && !$this->isValidIsbn13($isbn)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (strlen($isbn) !== 10) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            if (!ctype_digit($isbn[$i])) {
                return false;
            }
            $sum += (int) $isbn[$i] * (10 - $i);
        }

        $lastChar = $isbn[9];
        if ($lastChar === 'X') {
            $sum += 10;
        } elseif (ctype_digit($lastChar)) {
            $sum += (int) $lastChar;
        } else {
            return false;
        }

        return $sum % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (strlen($isbn) !== 13 || !ctype_digit($isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int) $isbn[12];
    }
}
