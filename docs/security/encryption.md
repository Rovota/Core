---
description: 'Namespace: Rovota\Core\Security | Rovota\Core\Facades'
---

# Encryption

### Introduction

Encryption is a key ingredient to keeping sensitive data private and secure. This is why Core provides a variety of methods to help you encrypt and decrypt data with ease. Additionally, various functionality within Core use encryption by default, like [cookies](../advanced/cookies.md) and [two factor](two-factor.md) support.&#x20;

All data encrypted using this functionality uses the configured cipher and key, and is signed using a message authentication code (MAC). This prevents decryption of data that someone has modified or tampered with.

### Ciphers

Currently, we support the use of the following ciphers:

* AES-128-CBC
* AES-256-CBC
* AES-128-GCM
* AES-256-GCM

{% hint style="info" %}
Our own products use **AES-256-GCM** and create a corresponding key during installation. We strongly recommend using the same cipher, since it provides a good balance of security and performance.
{% endhint %}

### Configuration

Before using built-in encryption functionality, you must have an encryption key and cipher configured within your `config/encryption.php` file. For more information, [read this article](../getting-started/configuration/encryption.md).

{% hint style="success" %}
When installing any of our products, these values are automatically set.
{% endhint %}

### Methods

The following methods are available for encryption functionality:

| [decrypt](encryption.md#decrypt)             | [encrypt](encryption.md#encrypt)             | [generateKey](encryption.md#generatekey) |
| -------------------------------------------- | -------------------------------------------- | ---------------------------------------- |
| [decryptString](encryption.md#decryptstring) | [encryptString](encryption.md#decryptstring) | [supports](encryption.md#supports)       |

### Examples

#### `decrypt()`

```php
use Rovota\Core\Facades\Crypt;
use Rovota\Core\Security\Exceptions\PayloadException;

try {
    $value = Crypt::decrypt('eyJpdiI6 ...');
    // new User(['username' => 'William'])
} catch (PayloadException $exception) {
    // handle gracefully
}
```

{% hint style="danger" %}
When the given value couldn't be decrypted, the `PayloadException` will be thrown.
{% endhint %}

#### `decryptString()`

Works exactly like [`decrypt()`](encryption.md#decrypt), but only returns a string.

#### `encrypt()`

```php
use Rovota\Core\Facades\Crypt;
use Rovota\Core\Security\Exceptions\EncryptionException;

try {
    $encrpyted = Crypt::encrypt(new User(['username' => 'William']));
    // eyJpdiI6 ...
} catch (EncryptionException $exception) {
    // handle gracefully
}
```

{% hint style="danger" %}
When the given value couldn't be encrypted, the `EncryptionException` will be thrown.
{% endhint %}

#### `encryptString()`

Works exactly like [`encrypt()`](encryption.md#encrypt), but only accepts a string as parameter.

#### `generateKey()`

Generates a new cryptographically secure key that can be used to encrypt/decrypt data.

```php
use Rovota\Core\Facades\Crypt;

$key = Crypt::generateKey('AES-256-GCM'); // This can be any of the supported ciphers
```

{% hint style="warning" %}
This method will return the raw key, which is difficult to store. Use `base64_encode()` before storing or using the key within config files.
{% endhint %}

#### `supports()`

Checks whether the given key is supported by the cipher provided. This method will return false when a key has been created for a different cipher than currently provided.

```php
use Rovota\Core\Facades\Crypt;

$bool = Crypt::supports($key, $cipher);
```
