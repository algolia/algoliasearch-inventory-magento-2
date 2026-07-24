# CHANGE LOG

## 1.5.0
- Updated for compatibility with 3.19.0
- Fixeed fatal `TypeError` on reindex caused by core module's `RecordBuilder::__construct()` signature change (added `ProductResource` and `GalleryReadHandler`); `InventoryProductRecordBuilder` now forwards both to the parent constructor

## 1.4.0
- Fix compatibility with versions 3.16.3 and 3.17.3 of the Algolia_AlgoliaSearch module 

## 1.1.0
- Fix compatibility with version 3.14.0 of the Algolia_AlgoliaSearch module (thanks @thomas-kl1)

## 1.0.4
- Fixed in_stock status issue

## 1.0.3

- Fixed the compatibility issue with magento version 2.4.5

## 1.0.2

- Updated Readme and dependencies

## 1.0.1

- Updated composer dependencies

## 1.0.0

- Release
