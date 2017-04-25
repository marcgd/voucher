<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Lib\Voucher\VoucherGenerator;

class VoucherTest extends KernelTestCase
{
    public function test_generate_voucher_full_length()
    {
        $voucher = new VoucherGenerator();

        $result = $voucher->generate();

        assert(strlen($result) == 13);
    }

    public function test_generate_voucher_short_length()
    {
        $voucher = new VoucherGenerator();

        $result = $voucher->generate(4);

        assert(strlen($result) == 4);
    }
}
