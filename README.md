# DO NOT USE - IN DEVELOPMENT

ETL
===

AntiMattr ETL is a library.

Installation
============

Add the following to your composer.json file:

```json
{
    "require": {
        "antimattr/etl": "dev-master"
    }
}
```

Install the libraries by running:

```bash
composer install
```

If everything worked, the ETL can now be found at vendor/antimattr/etl.

Features
========

 * Create a simplified interface for interaction with etl tasks

   ```php
   $processor->run($taskName, $options);
   ```

 * Console command will read yaml configurations and run the processor

   ```bash
   ./demo/console antimattr:etl:execute mongodb_mysql sellables,products
   ```

Model
=====

```javascript
TaskTrait = {
  extractor: ExtractorTrait,
  loader: LoaderTrait,
  transformations: [
    TransformationTrait,
  ],
};

ExtractorTrait = {
  connection: Data Source Connection,
  task: Parent Task,
  extract: abstract method,
};

LoaderTrait = {
  connection: Data Destination Connection,
  task: Parent Task,
  load: abstract method,
  postLoad: abstract method,
};

TransformationTrait = {
  field: string,
  name: string (unique identifier),
  task: Parent Task,
  transformers: [
    TransformerTrait,
  ]
  postTransform: public method,
};

TransformerTrait = {
  transformation: Parent Transformation,
  transform: abstract method,
  postTransform: public method,
};
```

Demo
====

Example Command and configurations

Try:

```bash
./demo/console antimattr:etl:execute mongodb_mysql sellables
```

Pull Requests
=============

Pull Requests - PSR Standards
-----------------------------

Please use the pre-commit hook to run the fix all code to PSR standards

Install once with

```bash
./bin/install.sh
Copying /antimattr-etl/bin/pre-commit.sh -> /antimattr-etl/bin/../.git/hooks/pre-commit
```

Pull Requests - Testing
-----------------------

Please make sure tests pass

```bash
$ vendor/bin/phpunit tests
```

Pull Requests - Code Sniffer and Fixer
--------------------------------------

Don't have the pre-commit hook running, please make sure to run the fixer/sniffer manually

```bash
$ vendor/bin/php-cs-fixer fix src/
$ vendor/bin/php-cs-fixer fix tests/
