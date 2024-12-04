# Magento 2 Encryption Key Manager CLI

**A utility for Magento 2 encryption key rotation and management. CVE-2024-34102(aka Cosmic Sting) victims can use it as an aftercare.**

<a href="https://www.wubinworks.com/encryption-key-manager-cli.html" target="_blank"><img src="https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/EncryptionKeyManagerCli/encrption-key-manager-cli.jpg" alt="Wubinworks Magento 2 Encryption Key Manager CLI" title="Wubinworks Magento 2 Encryption Key Manager CLI" /></a>

## Designed for

 - Development usage
 - Deployment automation
 - CVE-2024-34102(aka Cosmic Sting) aftercare

#### CVE-2024-34102(aka Cosmic Sting)

After applying security patches, you need to perform a key rotation to completely deny the attacker's Admin level WebAPI access.

If you cannot upgrade or apply the official isolated patch, see [Our Patches](#you-may-also-like).

If the official encryption key rotation command `php bin/magento encryption:key:change` is not available, you can use this extension and this extension has more features as a "Key Manager".

## Usage

**This extension offers 3 commands.**

 - Generate new encryption key(for development/scripting purpose)

```
php bin/magento ww:encryption-key-manager:genkey [-f|--format FORMAT]
```
Example:
```
$ php bin/magento ww:encryption-key-manager:genkey
5f81fe506a1025b8ea439fd49c6fa8e3
```

 - List all/newest encryption keys

```
php bin/magento ww:ekm:list [--newest]
```
*Tip: you can use `ekm` shorthand for `encryption-key-manager`.*

Example:
```
$ php bin/magento ww:ekm:list
Encryption key count: 3
39a2f1213e6a942af3cd4f1c2d61528c
fdd862cd41f95e4edaf2636258ce359f
3cd27f0eeae9ffec35681d8aa0faa618
```

 - Encryption key rotation (most important)

```
php bin/magento ww:encryption-key-manager:rotate [-k|--key KEY]
```
*Tip: if `-k|--key` is not provided, a random generated key will be used.*

Example:
```
$ php bin/magento ww:encryption-key-manager:rotate
Encryption key has been rotated successfully.
Encryption keys are stored in `app/etc/env.php`. Caution: do not delete old keys!
```

## New Encryption Key Format

Starting from version 2.4.7, encryption key format is changed from `hex` to `base64`.

New format example(note it has a `base64` prefix):
```
base64bDr+HSz4tZ+cjZA89J5RvbZzCfDKWO1iXgDfmqeZL0c=
```

By default, `php bin/magento ww:encryption-key-manager:genkey` generates a key that is compatible with your **current Magento version**.

But you can force the format(for development purpose)
```
php bin/magento ww:encryption-key-manager:genkey --format base64
php bin/magento ww:encryption-key-manager:genkey --format hex
```

More details of the key generation process are in this [blog post](https://www.wubinworks.com/blog/post/new-encryption-key-format-introduced-on-magento-2.4.7).

## Requirements

**Magento 2.4**

## Installation

**`composer require wubinworks/module-encryption-key-manager-cli`**

## ♥

If you like this extension please star this repository.

## You May Also Like

[Magento 2 patch for CVE-2024-34102(aka Cosmic Sting)](https://github.com/wubinworks/magento2-cosmic-sting-patch)

[Magento 2 JWT Authentication Patch](https://github.com/wubinworks/magento2-jwt-auth-patch)
