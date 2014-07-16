zf2-di
======

Alternative, slimmed down dependency injector for Zend Framework 2

The default dependency injector (di) for ZF2 is a bit heavy, as well as tightly coupled with the framework. I wanted to create a very simple-to-use, slim, fast, portable injector to use in multiple projects.

Allows injecting of:
   * config values
   * other objects initialized by the injector
   * other objects or services initialized elsewhere in the application

Allows for nested config settings.

Includes a required flag, allowing optional dependencies.

Prevents, and throws exeption on circular dependencies.
