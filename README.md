# Semantic Notifications

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticNotifications.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticNotifications)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticNotifications/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticNotifications/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticNotifications/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticNotifications/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-notifications/version.png)](https://packagist.org/packages/mediawiki/semantic-notifications)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-notifications/d/total.png)](https://packagist.org/packages/mediawiki/semantic-notifications)

Semantic Notifications (a.k.a. SNO) is a [Semantic Mediawiki][smw] extension that can inform registered users about
changes to their structured data with the help of notifications send by the [Echo][echo] extension.

Support for notifications on:

- Changes for selected properties and specific values
- Changes for selected categories
- Changes to the specification and declaration of a property, category or concept

## Requirements

- PHP 5.6 or later
- MediaWiki 1.31 or later
- [Echo (Notifications)][echo] ??
- [Semantic MediaWiki][smw] 3.0 or later

## Installation

The recommended way to install Semantic Notifications is by using [Composer][composer] with:

```json
{
	"require": {
		"mediawiki/semantic-notifications": "~1.0"
	}
}
```
1. From your MediaWiki installation directory, execute
   `composer require mediawiki/semantic-notifications:~1.0`
2. Navigate to _Special:Version_ on your wiki and verify that the package
   have been successfully installed.

## Usage

![image](https://cloud.githubusercontent.com/assets/1245473/15995802/e43ae88c-3120-11e6-872c-e216d16b2739.png)

### Documentation

The [workflow document](docs/01-workflow.md) contains a detailed description about the required
settings and decisions a user has to make in order for him or her to receive notifications.

Additional tips and use cases can be found [here](docs/02-tips.md) while [this document](docs/03-technical.md)
describes some technical details.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticNotifications/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticNotifications/pulls)
* Ask a question on [the mailing list](https://www.semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Libera.

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[contributors]: https://github.com/SemanticMediaWiki/SemanticNotifications/graphs/contributors
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticNotifications
[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[composer]: https://getcomposer.org/
[echo]: https://www.mediawiki.org/wiki/Extension:Echo
