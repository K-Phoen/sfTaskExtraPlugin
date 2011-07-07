This plugin adds the following tasks to the Symfony CLI:


## Doctrine

  * `doctrine:build-app-filters`: Builds form filter classes in the application lib directory
  * `doctrine:build-app-forms`:   Builds form classes in the application lib directory


## Generator

  * `generate:controller`:    Generates a new front controller in the web directory
  * `generate:plugin`:        Generates a new plugin
    * the new plugin is ready _to extend_ the user ([see how](http://www.carpe-hora.com/2011/05/Symfony-plugin-extend-the-user/))
    * the new plugin is ready to dynamically connect its own routes (again, [see how](http://www.carpe-hora.com/2011/05/Symfony-plugin-the-routing/)).
      The routes autoloading can be disabled in the application, by setting the `app_##PLUGIN_NAME##_routes_register` option to `false`.
    * the plugin also comes with an improved functionnal testing system. See the **Plugin tests** part for more information.
  * `generate:plugin-module`: Generates a new module in a plugin
  * `generate:test`:          Generates a new unit test stub script

### Plugin tests

The original sfTaskExtraPlugin came with a _fixture application_ which was supposed to offer an easy way to test plugins withouth beeing
dependant on a Symfony project. It appears that it was not very easy to set up this kind of testing achitecture, so here we come !

In this fork, we provide an easy way to:

  * run all the tests in an isolated Symfony "_fixture project_"
  * use different Symfony versions for the tests

To achieve this, we added two options in the `test/bin/prove.php` file:

  * `Symfony`: include paths for Symfony. Multiple directories can be given, as long as they are separated by _:_. Only the first existing
    directory will be considered. If nothing is given or none of the given directories exist, we'll try to guess the Symfony's path. If we
    fail, an exception is raised.
  * `xml`: file in which the test results will be exported.

We also preconfigured the fixture project to work for Symfony project using Propel and a SQLite database. It means that after configuring the
fixture project (ie: defining the required plugins and enabling your modules), it will just work ... or at least it should !


## Plugin

  * `plugin:package`: Create a plugin PEAR package


## Propel

  * `propel:build-app-filters`: Builds form filter classes in the application lib directory
  * `propel:build-app-forms`:   Builds form classes in the application lib directory


## Subversion

  * `subversion:set-props`: Sets typical Subversion properties


## Test

  * `test:plugin`: Launches a plugin test suite
