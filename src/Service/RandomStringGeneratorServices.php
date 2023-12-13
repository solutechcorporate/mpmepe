<?php

declare(strict_types=1);

namespace App\Service;

class RandomStringGeneratorServices
{
    private readonly int $lengthRandomId;

    private readonly int $numberItteration;

    public function __construct()
    {
        $this->lengthRandomId = 6;
        $this->numberItteration = 2;
    }

    public function random_alphanumeric_custom_itteration($prefix = 'RAND')
    {
        $string = '';
        $itteration = $this->numberItteration;
        for ($i = 0; $i < $itteration; $i++) {
            $string .= $this->random_alphanumeric();
            if ($i < $itteration - 1) {
                $string .= '-';
            }
        }
        return $prefix . '-' . $string;
    }

    public function random_alphanumeric_full($prefix = 'RAND')
    {
        $string = '';
        $itteration = $this->numberItteration;
        for ($i = 0; $i < $itteration; $i++) {
            $string .= $this->random_alphanumeric();
            if ($i < $itteration - 1) {
                $string .= '-';
            }
        }
        return $prefix . '-' . $string;
    }

    public function random_alphanumeric($length = false)
    {
        if (! $length) {
            $length = $this->lengthRandomId;
        }
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345689';
        $my_string = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = random_int(0, strlen($chars) - 1);
            $my_string .= substr($chars, $pos, 1);
        }
        return $my_string;
    }

    public function random_alphanumeric_custom_length($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345689';
        $my_string = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = random_int(0, strlen($chars) - 1);
            $my_string .= substr($chars, $pos, 1);
        }
        return $my_string;
    }
}
