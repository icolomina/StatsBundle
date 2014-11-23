===========
StatsBundle
===========
Introduction
------------
StatsBundle allows developers to hit operation stats simply by annotating a class (which will acts as a service) and class methods (which will act as service operations). StatsBundle use MongoDB as a database endpoint where store stats.

Requirements
------------
This bundle requires doctrine annotations reader and JMS\AopBundle to be installed. If you install this bundle using composer then dependencies will be installed by composer. If you install it by using git submodule, make sure that  both annotations reader and JMS\AopBundle are installed in your vendor project.

Installation
------------
Use git submodules to install it::

    git submodule add https://github.com/icolomina/StatsBundle.git src/Ict/StatsBundle

Or use composer repositories option to install it::

   "repositories": [
        {
            "url": "https://github.com/icolomina/StatsBundle.git",
            "type": "git"
        }
    ],
 
    "require": {
       ........
       "ict/stats-bundle": "dev-master",
    }

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Ict\StatsBundle\IctStatsBundle(),
        // ...
    );

Make sure you also register JMS\AopBundle in your kernel

Usage
-----
Configuration Options
^^^^^^^^^^^^^^^^^^^^^
StatsBundle options must be configured under ict_stats entry in your config.yml file. The available options are the following:

- on_entry_method: Indicates what to do when annotated method is intercepted. Default value is "none" and it tells to the interceptor that do nothing. If "log" value is setted, then logger service will be used to log the entry method

- catch_exception: Indicates whether the interceptor has to catch exceptions that intercepted methods can throw. Values can be true or false

- on_throw_exception: Indicate what to do when an exception is throwed by the intercepted method. Values can be none (do nothing), log (simply log that an exception was throwed), throw (rethrow the exception but not log), throw_and_log (retrow the exception and log it)

- db_handler: Option group that can have the following sub-options:
  
   - type: Indicates the database handler which will be used to store stats. Types can be 
      - odm (bundle will use doctrine_mongodb to hit stats) 
      - php_mongo (bundle will use php mongo driver to hit stats)

   - store_endpoint_name: Indicates the name of the Stats endpoint name (In mongo case it indicates the collection name where stats will be stored)

   - store_endpoint_fields: Indicates the name of the fields
