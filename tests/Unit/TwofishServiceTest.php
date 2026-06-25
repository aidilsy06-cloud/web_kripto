<?php

namespace Tests\Unit;

use App\Services\TwofishService;
use Tests\TestCase;

class TwofishServiceTest extends TestCase
{
    protected TwofishService $twofish;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twofish = new TwofishService();
    }

    /**
     * Test key derivation using PBKDF2 with SHA-256.
     */
    public function test_key_derivation_yields_256_bit_key(): void
    {
        $password = "MasterPassword123!";
        $salt = base64_encode(random_bytes(24));

        $derivedKey = $this->twofish->deriveKey($password, $salt);

        // A 256-bit key should be exactly 32 bytes (binary string)
        $this->assertEquals(32, strlen($derivedKey));
    }

    /**
     * Test successful encryption and decryption.
     */
    public function test_encryption_and_decryption_are_consistent(): void
    {
        $password = "SecretPassword";
        $salt = base64_encode(random_bytes(24));
        $plaintext = "TulipCryptTwofishManager2026";

        $derivedKey = $this->twofish->deriveKey($password, $salt);
        $encryptedResult = $this->twofish->encrypt($plaintext, $derivedKey);

        $this->assertArrayHasKey('ciphertext', $encryptedResult);
        $this->assertArrayHasKey('iv', $encryptedResult);
        $this->assertNotEmpty($encryptedResult['ciphertext']);
        $this->assertNotEmpty($encryptedResult['iv']);

        $decrypted = $this->twofish->decrypt(
            $encryptedResult['ciphertext'],
            $derivedKey,
            $encryptedResult['iv']
        );

        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Test that two ciphertexts of the same plaintext with same key are different (CBC mode random IV).
     */
    public function test_cbc_random_iv_produces_different_ciphertexts(): void
    {
        $password = "SecretPassword";
        $salt = base64_encode(random_bytes(24));
        $plaintext = "SameSecretData";

        $derivedKey = $this->twofish->deriveKey($password, $salt);

        $encrypted1 = $this->twofish->encrypt($plaintext, $derivedKey);
        $encrypted2 = $this->twofish->encrypt($plaintext, $derivedKey);

        // Ciphertexts and IVs must be different for security
        $this->assertNotEquals($encrypted1['ciphertext'], $encrypted2['ciphertext']);
        $this->assertNotEquals($encrypted1['iv'], $encrypted2['iv']);

        // Both must decrypt back to the original plaintext
        $decrypted1 = $this->twofish->decrypt($encrypted1['ciphertext'], $derivedKey, $encrypted1['iv']);
        $decrypted2 = $this->twofish->decrypt($encrypted2['ciphertext'], $derivedKey, $encrypted2['iv']);

        $this->assertEquals($plaintext, $decrypted1);
        $this->assertEquals($plaintext, $decrypted2);
    }
}
