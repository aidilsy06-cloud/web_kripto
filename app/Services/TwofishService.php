<?php

namespace App\Services;

use phpseclib3\Crypt\Twofish;
use Exception;

/**
 * Service to handle Cryptographic operations using the Twofish Algorithm.
 * 
 * Twofish is a symmetric key block cipher with a block size of 128 bits (16 bytes)
 * and key sizes up to 256 bits (32 bytes). In this implementation, we use:
 * - Twofish-256 (256-bit key length for maximum security)
 * - CBC (Cipher Block Chaining) Mode of operation
 * - Random 16-byte Initialization Vectors (IV) per encryption
 * - PBKDF2 with SHA-256 for secure key derivation from Master Password
 */
class TwofishService
{
    /**
     * Derive a 256-bit (32-byte) key from a user's master password and a salt.
     * Uses PBKDF2 with SHA-256 and 10,000 iterations to prevent brute-force attacks.
     *
     * @param string $password The master password
     * @param string $salt Base64 encoded or raw salt
     * @return string Raw binary derived key (32 bytes)
     */
    public function deriveKey(string $password, string $salt): string
    {
        // Decode salt if it is base64 encoded, or use as is
        $binarySalt = $this->isBase64($salt) ? base64_decode($salt) : $salt;

        // Perform PBKDF2 key derivation (outputs 32 bytes = 256 bits)
        return hash_pbkdf2('sha256', $password, $binarySalt, 10000, 32, true);
    }

    /**
     * Encrypt plaintext data using Twofish-256 in CBC mode.
     *
     * @param string $plaintext Data to encrypt
     * @param string $key Raw binary 256-bit key
     * @return array Array containing base64 encoded 'ciphertext' and base64 encoded 'iv'
     */
    public function encrypt(string $plaintext, string $key): array
    {
        try {
            // Generate a random 16-byte IV (Block size of Twofish is 128 bits)
            $iv = random_bytes(16);

            // Initialize phpseclib's Twofish engine in CBC mode
            $twofish = new Twofish('cbc');
            $twofish->setKey($key);
            $twofish->setIV($iv);

            // Encrypt the plaintext
            $ciphertextBinary = $twofish->encrypt($plaintext);

            return [
                'ciphertext' => base64_encode($ciphertextBinary),
                'iv' => base64_encode($iv)
            ];
        } catch (Exception $e) {
            throw new Exception("Encryption failed: " . $e->getMessage());
        }
    }

    /**
     * Decrypt ciphertext data using Twofish-256 in CBC mode.
     *
     * @param string $ciphertextBase64 Base64 encoded ciphertext
     * @param string $key Raw binary 256-bit key
     * @param string $ivBase64 Base64 encoded IV
     * @return string Decrypted plaintext
     */
    public function decrypt(string $ciphertextBase64, string $key, string $ivBase64): string
    {
        try {
            $ciphertextBinary = base64_decode($ciphertextBase64);
            $iv = base64_decode($ivBase64);

            // Initialize Twofish engine
            $twofish = new Twofish('cbc');
            $twofish->setKey($key);
            $twofish->setIV($iv);

            // Decrypt and return plaintext
            return $twofish->decrypt($ciphertextBinary);
        } catch (Exception $e) {
            throw new Exception("Decryption failed. Please check the integrity of your key/IV. Error: " . $e->getMessage());
        }
    }

    /**
     * Check if a string is valid Base64.
     */
    private function isBase64(string $string): bool
    {
        return base64_encode(base64_decode($string, true)) === $string;
    }
}
