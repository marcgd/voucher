<?php

namespace AppBundle\Lib\Voucher;

class VoucherGenerator
{

    public function generate($length = 13)
    {
        // Generate a voucher code using uniqid PHP function
        $unique = uniqid();
        // Return last part of the string based on required length
        return $length >= 13 ? $unique : substr($unique, strlen($unique) - $length, strlen($unique));
    }
}
