<?php
/**
 * Hashing algorithm as used in myp archives. Taken from EC
 *
 * usage:
 * $ php name-to-hash.php 'data/gamedata/mobart.csv'
 */
$mask = 0xffffffff;

function p($i) {
    printf("%08x\n", $i & 0xffffffff);
}

/**
 * 32bit shift right
 */
function shr($v, $w) {
    return ($v & 0xffffffff) >> $w;
}

/**
 * 32bit add
 */
function add($a, $b) {
    return ($a & 0xffffffff) + ($b & 0xffffffff);
}

function sub($a, $b) {
    return ($a & 0xffffffff) - ($b & 0xffffffff);
}


$str = $argv[1];

//vars
$chunks;

$len = strlen($str);
printf("input: '%s' (%d bytes)\n", $str, $len);

$ecx = 0;
$eax = $len;

$esi = $eax + $ecx - 0x21524111;
$edi = $esi;
$ebx = $esi;
//p($esi);

$off = 0;
if ($len > 0xc) {
    $eax = $eax - 13;
    $edx = 0;
    $ebx = 0xc;

    $count = intval($eax / 0xc) + 1;
    $remain = $eax % 0xc;
    $eax = $count;
    $edx = $remain;
    $chunks = $eax;

    $ebx = $edi;
    //p($ebx);p($eax);p($edx);

    while ($chunks > 0) {
        $eax = ((((ord($str[$off + 7]) << 8) + ord($str[$off + 6])) << 8) + ord($str[$off + 5])) << 8;
        $edx =  ord($str[$off + 4]) + $edi;
        $edi = $edx + $eax;


        $eax = ((((ord($str[$off + 0xb]) << 8) + ord($str[$off + 0xa])) << 8) + ord($str[$off + 9])) << 8;
        $edx = ord($str[$off + 8]) + $esi;
        $esi = $edx  + $eax;
        //p($edi);p($esi);

        $edx = (((((ord($str[$off + 3]) << 8) + ord($str[$off + 2])) << 8) + ord($str[$off + 1])) << 8) + ord($str[$off + 0]);
        $edx -= $esi;

        $eax = shr($esi, 0x1c);
        $edx = add($edx, $ebx);
        $edx = $edx ^ $eax;

        $eax = $esi << 4;
        $edx = $edx ^ $eax;
        $esi = add($esi, $edi);
        $edi -= $edx;

        $eax = shr($edx, 0x1a);
        $edi = $edi ^ $eax;

        $eax = $edx << 6;
        $edi = $edi ^ $eax;
        $edx = add($edx, $esi);
        $esi -= $edi;

        $eax = shr($edi, 0x18);
        $esi = $esi ^ $eax;

        $eax = $edi << 8;
        $esi = $esi ^ $eax;
        $edi = add($edi, $edx);

        $eax = shr($esi, 0x10);
        $edx -= $esi;
        $edx = $edx ^ $eax;

        $eax = $esi << 0x10;
        $edx = $edx ^ $eax;

        $ebx = $edx;
        $esi = add($esi, $edi);
        $edi -= $ebx;

        $eax = $ebx << 0x13;
        $edi = $edi ^ $eax;

        $eax = shr($ebx, 0xd);
        $edi = $edi ^ $eax;

        $ebx = add($ebx, $esi);

        $eax = shr($edi, 0x1c);
        $esi -= $edi;
        $esi = $esi ^ $eax;

        $eax = $edi << 4;
        $esi = $esi ^ $eax;
        //------------- ok
        $eax = $len;
        $eax -= 0xc;
        $len = $eax;

        $edi = add($edi, $ebx);
        $off += 0xc;

        $chunks--;
    }

    switch($len) {
        case 0xc:
            $eax = ord($str[$off + 0xb]) << 0x18;
            $esi = add($esi, $eax);
        case 0xb:
            $eax = ord($str[$off + 0xa]) << 0x10;
            $esi = add($esi, $eax);
        case 0xa:
            $eax = ord($str[$off + 9]) << 8;
            $esi = add($esi, $eax);
        case 9:
            $eax = ord($str[$off + 8]);
            $esi = add($esi, $eax);
        case 8:
            $eax = ord($str[$off + 7]) << 0x18;
            $edi = add($edi, $eax);
        case 7:
            $eax = ord($str[$off + 6]) << 0x10;
            $edi = add($edi, $eax);
        case 6:
            $eax = ord($str[$off + 5]) << 8;
            $edi = add($edi, $eax);
        case 5:
            $eax = ord($str[$off + 4]);
            $edi = add($edi, $eax);
        case 4:
            $eax = ord($str[$off + 3]) << 0x18;
            $ebx = add($ebx, $eax);
        case 3:
            $eax = ord($str[$off + 2]) << 0x10;
            $ebx = add($ebx, $eax);
        case 2:
            $eax = ord($str[$off + 1]) << 8;
            $ebx = add($ebx, $eax);
        case 1:
            $eax = ord($str[$off + 0]);
            $ebx = add($ebx, $eax);
        default:
            //p($eax);p($ebx);p($edx);p($esi);p($edi);
            //printf("-----\n");

            $esi = $esi ^ $edi;
            $eax = shr($edi, 0x12);

            $ecx = $edi << 0xe;
            $eax = $eax ^ $ecx;

            $esi = sub($esi, $eax);
            $eax = shr($esi, 0x15);

            $ecx = $esi << 0xb;
            $eax = $eax ^ $ecx;

            $ecx = $esi ^ $ebx;
            $ecx = sub($ecx, $eax);

            $eax = $ecx;
            $edx = shr($ecx, 7);
            $edi = $edi ^ $ecx;

            $eax = $eax << 0x19;
            $eax = $eax ^ $edx;


            $edi = sub($edi, $eax);
            $esi = $esi ^ $edi;

            $eax = shr($edi, 0x10);
            $edx = $edi << 0x10;
            $eax = $eax ^ $edx;

            $esi = sub($esi, $eax);

            $eax = $esi;
            $edx = $eax << 4;
            $esi = shr($esi, 0x1c);
            $esi = $esi ^ $edx;

            $edx = $eax ^ $ecx;
            $edx = sub($edx, $esi);

            $ecx = shr($edx, 0x12);
            $esi = $edx << 0xe;

            $ecx = $ecx ^ $esi;
            $edi = $edi ^ $edx;

            $edi = sub($edi, $ecx);
            $ecx = $edi << 0x18;

            $edx = shr($edi, 8);
            $ecx = $ecx ^ $edx;
            $eax = $eax ^ $edi;
            $eax = sub($eax, $ecx);

            //p($eax);p($ebx);p($ecx);p($edx);p($esi);p($edi);
            //printf("-----\n");
        case 0:

            break;

    }

}

printf("hash: %08x %08x\n", $edi & $mask, $eax & $mask);

