Algolia Search Inventory for Magento 2.3.x and 2.4.x
==================

![Latest version](https://img.shields.io/badge/latest-1.5.0-green)

This Algolia_AlgoliaSearchInventory is a community-developed module to provide compatibility between Magento (2.3.x, 2.4.x) Inventory feature and Algolia Search 1.12+ extension. Though Algolia is a contributor to this repository, there is no product roadmap for this module and it’s not aligned with the Algolia/Magento integration product releases.

#### Compatibility

| Algolia Search for Magento 2                                                                                                                                                         | Required Extension Version |
|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------|
| \>=[3.8.1](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.8.1)                                                                                                    | 1.x                        |
| \>=[3.9.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.9.0), <3.10.3                                                                                           | 1.0.3                      |
| \>=[3.10.3](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.10.3), <3.14.0                                                                                         | ~1.0.5                     |
| ~[3.14.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.14.0)                                                                                                    | ~1.1.0                     |
| ~[3.15.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.15.0)                                                                                                    | ~1.2.0                     |
| \>=[3.16.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.16.0) <3.16.3, \>=[3.17.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.17.0) <3.17.3 | ~1.3.1                     |
| \>=[3.16.3](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.16.3) <3.17.0, \>=[3.17.3](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.17.3) <3.19.0 | ~1.4.1                     |
| \>=[3.19.0](https://github.com/algolia/algoliasearch-magento-2/releases/tag/3.19.0)                                                                                                  | ~1.5.0                     |

## Installation
The easiest way to install the extension is to use [Composer](https://getcomposer.org/)

Run the following commands:

- ```$ composer require algolia/algoliasearch-inventory-magento-2```
- ```$ bin/magento module:enable Algolia_AlgoliaSearchInventory```
- ```$ bin/magento setup:upgrade && bin/magento setup:static-content:deploy```

## Upgrades

When upgrading the [base Algolia extension](https://github.com/algolia/algoliasearch-magento-2) it is best to upgrade
 this inventory extension at the same time, e.g.

```
composer require \
    algolia/algoliasearch-magento-2:~3.19.0 \
    algolia/algoliasearch-inventory-magento-2 \
    --update-with-dependencies
```

This will ensure that the compatible version of the inventory extension is installed. 
