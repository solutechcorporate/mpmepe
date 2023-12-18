<?php

declare(strict_types=1);

namespace App\Service;

class ConvertValueToBoolService
{
    /**
     * Convertit une valeur en booléen
     *
     * @param mixed $value
     * @return string
     */
    public static function convertValueToBool(mixed $value): string
    {
        $retour = null;

        switch ($value) {
            case $value === false:
            case strtolower(trim((string) $value)) === "false":
            case trim((string) $value) === "":
            case strtolower(trim((string) $value)) === "0":
            case $value === 0:
            case strtolower(trim((string) $value)) === "null":
            case $value === null:
            case strtolower(trim((string) $value)) === "non":
            case strtolower(trim((string) $value)) === "faux":
            case strtolower(trim((string) $value)) === "":
                $retour = "0";
                break;
            case $value === true:
            case strtolower(trim((string) $value)) === "true":
            case strtolower(trim((string) $value)) === "1":
            case $value === 1:
            case strtolower(trim((string) $value)) === "oui":
            case strtolower(trim((string) $value)) === "vrai":
                $retour = "1";
                break;
        }

        return $retour;
    }

}
