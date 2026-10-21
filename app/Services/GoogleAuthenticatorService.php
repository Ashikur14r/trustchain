<?php

namespace App\Services;

class GoogleAuthenticatorService
{
    /**
     * Base32 character lookup map
     */
    private static string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new base32 secret key.
     */
    public static function generateSecret(int $length = 16): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32Chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Calculate 6-digit TOTP code for a secret at a given timestamp.
     */
    public static function getCode(string $secret, ?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $timeSlice = (int) floor($timestamp / 30);
        $secretKey = self::base32Decode($secret);

        // Pack time into 64-bit big-endian binary
        $time = pack('N*', 0) . pack('N*', $timeSlice);

        // Compute HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = $value % 1000000;

        return str_pad((string) $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a submitted 6-digit code within clock skew tolerance (+/- 1 step).
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $code = trim($code);
        $currentTime = time();

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calcTime = $currentTime + ($i * 30);
            if (hash_equals(self::getCode($secret, $calcTime), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate otpauth:// URI for Google Authenticator QR codes.
     */
    public static function getQrCodeUri(string $company, string $holder, string $secret): string
    {
        return 'otpauth://totp/' . rawurlencode($company) . ':' . rawurlencode($holder) .
               '?secret=' . rawurlencode($secret) .
               '&issuer=' . rawurlencode($company);
    }

    /**
     * Decode a base32 string into binary bytes.
     */
    private static function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos(self::$base32Chars, $b32[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
