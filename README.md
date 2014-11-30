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

Make sure you also register JMS\AopBundle in your kernel so that pointcut and interceptors work

Usage
-----
Configuration Options
^^^^^^^^^^^^^^^^^^^^^
StatsBundle options must be configured under ict_stats entry in your config.yml file. The available options are the following:

- on_entry_method: Indicates what to do when annotated method is intercepted. Default value is "none" and it tells to the interceptor that do nothing. If "log" value is setted, then logger service will be used to log the entry method

- catch_exception: Indicates whether the interceptor has to catch exceptions that intercepted methods can throw. Values can be true or false

- on_throw_exception: Indicates what to do when an exception is throwed by the intercepted method. Values can be none (do nothing), log (simply log that an exception was throwed), throw (rethrow the exception without logging), throw_and_log (retrow the exception and log it). This option only works if *catch_exception* options is true

- db_handler: Option group that can have the following sub-options:
  
   - type: Indicates the database handler which will be used to store stats. Types can be 
      - odm (bundle will use doctrine_mongodb to hit stats). You have to keep *doctrine_mongodb* configured correctly
      - php_mongo (bundle will use php mongo driver to hit stats). You have to keep *php-mongo* driver correctly installed

   - store_endpoint_name: Indicates the name of the Stats endpoint name (In mongo case it indicates the collection name where stats will be stored). If you use odm option you will have to indicate your stats document name. For instance: *AcmeDemoBundle:Stats*

   - store_endpoint_fields: Indicates the name of your *date*, *hour* and *ip* fields in your stats collection
   - php_mongo_connection_params: This option is only allowed when using *php_mongo* type option. You will have to indicate the *uri*, *db_name*, *options* and *driver_options* options in order to allow \MongoClient to connect and deal with your mongo database.

The following are two configuration examples. One using *php_mongo* type and the other one using *odm* type
::

   ict_stats:
     on_entry_method: log
     on_throw_exception: throw_and_log
     catch_exception: true
     db_handler:
       type: php_mongo
       store_endpoint_name: Stat
       store_endpoint_fields:
         date_field: date
         hour_field: hour
         ip_field: ip
       php_mongo_connection_params:
         uri: mongodb://localhost:27017
         db_name: mydb
         options: ~
         driver_options: ~

   ict_stats:
     on_entry_method: log
     on_throw_exception: throw_and_log
     catch_exception: true
     db_handler:
       type: odm
       store_endpoint_name: Stat
       store_endpoint_fields:
         date_field: date
         hour_field: hour
         ip_field: ip

Stats inserts are made by using *w* option setted to 0. If you want to change it, place the option *ins_write_concerns* under *db_handler* and set it to 1.

Making classes and methods stateables
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Now you have your configuration ready, let's begin to hit stats. Imagine you have a *Mailer* class which keeps a *send* method and you would like to hit an stat when a new mail is sent::

  class Mailer {

     .....

     public function send()
     {

     }

     .....
  }

You can achieve that only by annotating class and method like this::

   use Ict\StatsBundle\Annotation as Stat;    

   /**
   * @Stat\Stateable(service="mailing")
   */
   class Mailer {

     .....

     /**
     * @Stat\Operation(service="mail_sent")
     */
     public function send()
     {

     }

     .....
  }

When Mailer:send method execution is intercepted in your application, an stat will be "hitted" to the stats collection making a document with the following structure

::

    _id: ObjectId("...........")
    date: ISODate("...........")
    hour: XX
    ip: XXX.XXX.XXX.XXX
    service: "mailing"
    operation:
       mail_sent: 1

Then when a new mail is sent, a new hit will be sent to the stats collection incrementing *mail_sent* operation to 2. If you annotate another method, for instance *error()* method in mailer class, and set it as *sending_error* your collection will be able to increment a new operation into mailing service:

:: 

    _id: ObjectId("...........")
    date: ISODate("...........")
    hour: XX
    ip: XXX.XXX.XXX.XXX
    service: "mailing"
    operation:
       mail_sent: 2
       sending_error: 1


Todo
----
- Create more tests
- Add routes to see stats information using charts and tables
- Add more database managers (Redis, CouchDB .....)

Feel free for contributing. It is my first bundle and all advices and contributions will be wellcome ;)
