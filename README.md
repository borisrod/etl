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

The execution flow of the processor is:

```text
Processor::executeTask
  => getExtractor::getPages
     => foreach page
        => getTransformations
           => foreach transformation
              => getTransformers
                 => transform
                 => bind
        => getLoader::load
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
