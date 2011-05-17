This plugin adds the following tasks to the symfony CLI:

Doctrine
--------

  * `doctrine:build-app-filters`: Builds form filter classes in the application lib directory
  * `doctrine:build-app-forms`:   Builds form classes in the application lib directory

Generator
---------

  * `generate:controller`:    Generates a new front controller in the web directory
  * `generate:plugin`:        Generates a new plugin
  * `generate:plugin-module`: Generates a new module in a plugin
  * `generate:test`:          Generates a new unit test stub script

Plugin
------

  * `plugin:package`: Create a plugin PEAR package

Propel
------

  * `propel:build-app-filters`: Builds form filter classes in the application lib directory
  * `propel:build-app-forms`:   Builds form classes in the application lib directory

Subversion
----------

  * `subversion:set-props`: Sets typical Subversion properties

Test
----

  * `test:plugin`: Launches a plugin test suite


Changes from the original plugin
--------------------------------

 * unit and functional tests bootstraps try to guess the symfony location (if
   not given through `$_SERVER['SYMFONY']`). If the symfony dir is still not
   found, an exception is raised.
