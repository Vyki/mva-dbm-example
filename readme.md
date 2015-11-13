Mva\Dbm example application
===========================

Mva\Dbm example application based on Nette framework and [MongoDB example data model](https://docs.mongodb.org/getting-started/shell/import-data/).

### Download repository
```bash
$ git clone git@github.com:Vyki/mva-dbm-example.git
```
### Install libraries
Require:
 - PHP >= 5.5.0
 - [MongoDB >= 2.6.0](https://docs.mongodb.org/manual/release-notes/2.6/)
 - [PECL MongoDB driver >= 1.6.0](https://pecl.php.net/package/mongo)
 - [Nette Framework ~ 2.3](https://github.com/nette/nette)
 - [Mva\Dbm ~ 1.1](https://github.com/Vyki/mva-dbm)
```bash
$ composer install
```
### Create test database and collection
```bash
$ cd ./bin
$ php create-db.php
```