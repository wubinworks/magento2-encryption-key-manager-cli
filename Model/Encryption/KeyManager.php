<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\EncryptionKeyManagerCli\Model\Encryption;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * All Magento 2.4 compatible encryption key manager
 */
class KeyManager
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var string
     */
    protected $keyPrefix;

    /**
     * Constructor
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
        $this->_init();
    }

    /**
     * Get Magento 2.4.7 introduced encryption key prefix
     *
     * @return void
     */
    protected function _init(): void
    {
        if (defined(ConfigOptionsListConstants::class . '::' . 'STORE_KEY_ENCODED_RANDOM_STRING_PREFIX')) {
            // >= Magento 2.4.7
            $this->keyPrefix = ConfigOptionsListConstants::STORE_KEY_ENCODED_RANDOM_STRING_PREFIX; // 'base64'
        } else {
            $this->keyPrefix = 'base64';
        }
    }

    /**
     * Get compatible format
     *
     * @return string base64|hex
     */
    protected function getCompatibleFormat(): string
    {
        if (defined(ConfigOptionsListConstants::class . '::' . 'STORE_KEY_ENCODED_RANDOM_STRING_PREFIX')) {
            // >= Magento 2.4.7
            return $this->keyPrefix;
        } else {
            return 'hex';
        }
    }

    /**
     * Generate key
     *
     * @param string $format '': current Magento version compatible format, 'hex': for < 2.4.7, 'base64': for >= 2.4.7
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function generate(string $format = ''): string
    {
        $format = strtolower($format);
        if ($format === '') {
            $format = $this->getCompatibleFormat();
        }

        switch ($format) {
            case $this->keyPrefix:
                // length is SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_KEYBYTES, 32
                $length = ConfigOptionsListConstants::STORE_KEY_RANDOM_STRING_SIZE;
                $key = $format . base64_encode(random_bytes($length));
                break;
            case 'hex':
                $length = 16;
                $key = bin2hex(random_bytes($length));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Unknown encryption key format: %s. Available formats are `base64` and `hex`',
                        $format
                    )
                );
        }

        return $key;
    }

    /**
     * Validate key
     *
     * @param string $key
     * @return bool
     */
    public function validate(string $key): bool
    {
        try {
            $this->encryptor->validateKey($key);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get keys
     *
     * @param bool $asString
     * @return array|string
     */
    public function getKeys(bool $asString = false)
    {
        $keys = $this->encryptor->exportKeys();
        if ($asString) {
            return $keys;
        }

        return $keys ? explode("\n", $keys) : [];
    }
}
