# DO NOT USE - IN DEVELOPMENT

ETL
===

AntiMattr ETL is a library for extracting, transforming, and loading data. Currently supports ETL from MongoDB to MySQL. Other data sources to follow.

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

Overview
========

Each Task should implement an ExtractorInterface, TransformationInterface, LoaderInterface

Example:

```yaml
services:
  # Extractors
  antimattr.etl_extractor.supplier_billing_address_max_updated_at:
    class: AntiMattr\ETL\Extract\MongoDB\PDOMaximumColumnDateExtractor
    lazy: true
    public: false
    arguments:
      - @antimattr.etl_mongodb_database # Data Extract
      - 'suppliers' # Collection Name
      - 'updatedAt' # Max MongoDB Property
      - @antimattr.etl_mysql_connection # Data Load
      - 'supplier_address' # Table Name
      - 'updatedAt' # Max MySQL Property
      - "WHERE addressType='billingAddress'" # Optional WHERE condition
      - '2000-01-01 EDT' # Optional Default Max MySQL Value
      - { '_id': 1, 'billingAddress': 1 } # Projection
      - { 'updatedAt': -1 } # Sort
      - 500 # Batch
    calls:
      - [ 'setTimezone', [ 'America/New_York' ] ]

  # Loaders
  antimattr.etl_loader.supplier_billing_address:
    class: AntiMattr\ETL\Load\MySQL\MySQLReplaceIntoLoader
    lazy: true
    public: false
    arguments: [ @antimattr.etl_mysql_connection, 'supplier_address' ]

  # Tasks
  antimattr.etl_task.supplier_billing_address:
    class: "%antimattr.etl_task_common.class%"
    lazy: true
    calls:
      - [ 'setExtractor', [ @antimattr.etl_extractor.supplier_billing_address_max_updated_at ] ]
      - [ 'setLoader', [ @antimattr.etl_loader.supplier_billing_address ] ]
    properties:
      configuration:
        -
          field: '_id'
          name: 'supplierId'
          transformers:
            - @antimattr.etl_transformer.mongodb.mongoid
        -
          field: '_id'
          name: 'addressType'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\NotNull\NotNullToDefaultTransformer
              properties:
                options: { 'default': 'billingAddress' }
        -
          field: 'billingAddress'
          name: 'name'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'name' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 255 }
        -
          field: 'billingAddress'
          name: 'address1'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'address1' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 255 }
        -
          field: 'billingAddress'
          name: 'address2'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'address2' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 255 }
        -
          field: 'billingAddress'
          name: 'city'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'city' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 255 }
        -
          field: 'billingAddress'
          name: 'state'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'state' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 255 }
        -
          field: 'billingAddress'
          name: 'state'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'state' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 50 }
        -
          field: 'billingAddress'
          name: 'zip'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'zip' }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 20 }
        -
          field: 'billingAddress'
          name: 'phone'
          transformers:
            -
              class: AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer
              properties:
                options: { 'field': 'phone' }
            - { class: OpenSky\ETL\Transform\Transformer\MongoDB\PhoneTransformer }
            - @antimattr.etl_transformer.scalar.html_utf8
            -
              class: AntiMattr\ETL\Transform\Transformer\Scalar\MaxLengthTransformer
              properties:
                options: { 'maxlength': 30 }
        -
          field: '_id'
          name: 'updatedAt'
          transformers:
            - @antimattr.etl_transformer.notnull.notnull_date
```

Features
========

 * A simplified interface for interaction with etl tasks

   ```php
   $processor->run($taskName, $options);
   ```

 * Console command will read yaml configurations and run the processor

    ```php
    #!/usr/bin/env php
    <?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

    $container = new ContainerBuilder();
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
    $loader->load('etl.yml');

    // The Command
    $command = new AntiMattr\ETL\Tools\Console\Command\ExecuteTaskCommand();
    $command->setContainer($container);

    // The console app
    $console = new Symfony\Component\Console\Application('AntiMattr ETL', '1.0');
    $console->add($command);
    $console->run();
    ```

    From the command line:

    ```bash
    ./demo/console antimattr:etl:execute mongodb_mysql --task=sellables --task=products --task=suppliers
    ```

 * Define your ETL recipe with Dependency Injection (see demo)

Model
=====

```javascript
TaskInterface = {
    function initialize(); # Initialize dependencies from configuration array
    function getData(); # return DataInterface
    function setData(DataInterface);
    function setDefaultTransformationClass(string);
    function getExtractor(); # return ExtractorInterface
    function setExtractor(ExtractorInterface);
    function getLoader(); # return LoaderInterface
    function setLoader(LoaderInterface);
    function getOptions(); # return array
    function setOptions(array);
    function addTransformation(TransformationInterface);
    function getTransformations(); # return Collection
    function setTransformations(Collection);
};

ExtractorInterface = {
    public function getPages(); # return Collection
    public function setPerPage(integer);
    public function setTask(TaskInterface);
    public function getTask(); # return TaskInterface
}

LoaderInterface = {
    public function load();
    public function postLoad();
    public function setTask(TaskInterface);
    public function getTask(); # return TaskInterface
}

TransformationInterface = {
    public function shouldContinue(); # Interrupts current transformation when TransformationContinueException is thrown
    public function initialize(array);
    public function postTransform();
    public function getDefaultTransformerClass(); # return string
    public function getDefaultValue(); # return mixed
    public function getField(); # return String identifying property name from data extract
    public function getName(); # return String identifying property name for data load
    public function setTask(TaskInterface);
    public function getTask(); # return TaskInterface
    public function addTransformer(TransformerInterface);
    public function getTransformers(); # return Collection
    public function setTransformers(Collection);
};

TransformerInterface = {
    public function bind(mixed, TransformationInterface);
    public function transform(mixed, TransformationInterface); # return mixed
};

# Track each iteration, the context of the Task, the extracted data, and the transformed data
DataInterface = {
    public function setCurrentExtractedRecord(array);
    public function getCurrentExtractedRecord(); # return array
    public function setCurrentIteration(mixed);
    public function getCurrentIteration(); # return mixed
    public function setExtracted(array);
    public function getExtractedCount(); # return integer
    public function getExtracted(); # return array
    public function getLoadedCount(); # return integer
    public function setLoadedCount(integer);
    public function setTransformed(array);
    public function getTransformedCount(); # return integer
    public function getTransformed(); # return array
    public function mergeTransformed(array); # Use this method to override a previously transformed iteration
    public function unsetTransformedOffset(mixed); # Use this method to remove a previously transformed iteration
    public function unsetExtractedOffset(mixed);
    public function setTask(TaskInterface);
    public function getTask(); # return TaskInterface
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
