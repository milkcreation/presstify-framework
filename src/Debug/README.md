# Theme Suite PresstiFy Plugin

[![Latest Version](https://img.shields.io/badge/release-2.0.9-blue?style=for-the-badge)](https://svn.tigreblanc.fr/presstify-plugins/theme-suite/tags/2.0.9)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

**Theme Suite** is a large collection of PresstiFy components dedicated to building apps.

## Installation

```bash
composer require presstify-plugins/theme-suite
```

## Setup

### Declaration

```php
// config/app.php
return [
      //...
      'providers' => [
          //...
          \tiFy\Plugins\ThemeSuite\ThemeSuiteServiceProvider::class,
          //...
      ];
      // ...
];
```

### Configuration

```php
// config/theme-suite.php
// @see /vendor/presstify-plugins/theme-suite/config/theme-suite.php
return [
      //...

      // ...
];
```
