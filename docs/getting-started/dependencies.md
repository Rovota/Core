---
description: A complete list of the currently required and optional dependencies for Core.
---

# Dependencies

### Introduction

Like many projects, we use various packages created by others in the community to speed up development and reduce redundant efforts. For example, it doesn't make sense for us to write an entire mail library when [PHPMailer](https://github.com/PHPMailer/PHPMailer) serves us near perfectly.

All of the packages listed below are encapsulated with some sort of abstraction layer. While this may seem counter effective, it makes it possible for us to guarantee compatibility and provide additional functionality. In order to give you flexibility, many of these abstractions provide methods to access underlying implementations/dependencies.

### Required

The following packages and extensions are essential to let Core function as it is supposed to.

{% tabs %}
{% tab title="Packages" %}
| Name                                                                                                                                                    | Supported version(s) |
| ------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------- |
| <p><strong>CommonMark</strong><br>(<a href="https://github.com/thephpleague/commonmark"><code>league/commonmark</code></a>)</p>                         | >=2.3.0 <3.0.0       |
| <p><strong>Dot Access Data</strong><br>(<a href="https://github.com/dflydev/dflydev-dot-access-data"><code>dflydev/dot-access-data</code></a>)</p>      | >=3.0.0 <4.0.0       |
| <p><strong>FluentPDO</strong><br>(<a href="https://github.com/envms/fluentpdo"><code>envms/fluentpdo</code></a>)</p>                                    | >=2.2.0 <3.0.0       |
| <p><strong>Flystem ReadOnly</strong><br>(<a href="https://github.com/thephpleague/flysystem-read-only"><code>league/flysystem-read-only</code></a>)</p> | >=3.3.0 <4.0.0       |
| <p><strong>Flysystem</strong><br>(<a href="https://github.com/thephpleague/flysystem"><code>league/flysystem</code></a>)</p>                            | >=3.10.0 <4.0.0      |
| <p><strong>GuzzleHTTP</strong><br>(<a href="https://github.com/guzzle/guzzle"><code>guzzlehttp/guzzle</code></a>)</p>                                   | >=7.5.0 <8.0.0       |
| <p><strong>Monolog</strong><br>(<a href="https://github.com/Seldaek/monolog"><code>monolog/monolog</code></a>)</p>                                      | >=3.2.0 <4.0.0       |
| <p><strong>OTPHP</strong><br>(<a href="https://github.com/Spomky-Labs/otphp"><code>spomky-labs/otphp</code></a>)</p>                                    | >=11.0.0 <12.0.0     |
| <p><strong>PHP Secure Communications Library</strong><br>(<a href="https://github.com/phpseclib/phpseclib"><code>phpseclib/phpseclib</code></a>)</p>    | >=3.0.0 <4.0.0       |
| <p><strong>PHPMailer</strong><br>(<a href="https://github.com/PHPMailer/PHPMailer"><code>phpmailer/phpmailer</code></a>)</p>                            | >=6.6.0 <7.0.0       |
| <p><strong>Semantic Versioning</strong><br>(<a href="https://github.com/PHLAK/SemVer"><code>phlak/semver</code></a>)</p>                                | >=4.0.0 <5.0.0       |
{% endtab %}

{% tab title="Extensions" %}
| Name                          | Supported version(s) |
| ----------------------------- | -------------------- |
| **cURL** (`ext-curl`)         | Any                  |
| **EXIF** (`ext-exif`)         | Any                  |
| **FileInfo** (`ext-fileinfo`) | Any                  |
| **MbString** (`ext-mbstring`) | Any                  |
| **OpenSSL** (`ext-openssl`)   | Any                  |
| **PDO** (`ext-pdo`)           | Any                  |
| **Sodium** (`ext-sodium`)     | Any                  |
| **ZIP** (`ext-zip`)           | Any                  |
{% endtab %}
{% endtabs %}

### Optional

The following dependencies aren't strictly required in order to use Core. However, they may be required when you intend to use specific functionality that depends on them.

{% tabs %}
{% tab title="Packages" %}
| Name                                                                                                                                                    | Supported version(s) |
| ------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------- |
| <p><strong>Flysystem AWS S3</strong><br>(<a href="https://github.com/thephpleague/flysystem-aws-s3-v3"><code>league/flysystem-aws-s3-v3</code></a>)</p> | >=3.10.0 <4.0.0      |
| <p><strong>Flysystem SFTP</strong><br>(<a href="https://github.com/thephpleague/flysystem-sftp-v3"><code>league/flysystem-sftp-v3</code></a>)</p>       | >=3.6.0 <4.0.0       |
{% endtab %}

{% tab title="Extensions" %}
| Name                        | Supported version(s) |
| --------------------------- | -------------------- |
| **Redis** (`ext-redis`)     | Any                  |
| **APCu** (`ext-apcu`)       | Any                  |
| **Imagick** (`ext-imagick`) | Any                  |
{% endtab %}
{% endtabs %}

### Replacements

The following packages can be safely ignored/removed since Core either replaces their functionality or because they are redundant in combination with supported PHP versions:

* `symfony/polyfill-php80`
* `symfony/polyfill-mbstring`
* `ralouphie/getallheaders`

### Conflicts

When we discover packages that conflict with Core, we'll list them here.
