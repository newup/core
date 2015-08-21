## NewUp Core

[![Build Status](https://travis-ci.org/newup/core.svg)](https://travis-ci.org/newup/core)
[![Latest Stable Version](https://poser.pugx.org/newup/core/v/stable.svg)](https://packagist.org/packages/newup/core)
[![Total Downloads](https://poser.pugx.org/newup/core/downloads.svg)](https://packagist.org/packages/newup/core)
[![Latest Unstable Version](https://poser.pugx.org/newup/core/v/unstable.svg)](https://packagist.org/packages/newup/core)
[![License](https://poser.pugx.org/newup/core/license.svg)](https://packagist.org/packages/newup/core)

NewUp is a command line utility that makes creating packages quick, easy and universal. NewUp attempts to make creating packages for every programming language, framework, platform, etc as painless as possible. Just because NewUp is written in PHP, does not mean generated packages have to be PHP.

## What Is This Repository?

This repository contains the core framework and utilities for the [NewUp Command Line Utility](https://github.com/newup/newup). This is where you want to be if you are interested in how NewUp works under the hood. If you simply want to install NewUp for general usage you should head on over to [https://github.com/newup/newup](https://github.com/newup/newup) where you will find installation and configuration instructions.

For example, in this repository you will find the code behind the template engine (built around [Twig](http://twig.sensiolabs.org/)), the package template storage engine (utilizes [Composer](https://getcomposer.org/)), the directory generator and many other components.

## Playground Examples

The [newup-playground](https://github.com/newup-playground) GitHub organization contains various examples of package templates implemented using NewUp. Here is a list of examples (the list may not be exhaustive):

| Playground | Description |
|------------|-------------|
| [newup-playground/alpha](https://github.com/newup-playground/alpha) | Demonstrates that NewUp can load package template dependencies. |
| [newup-playground/workbench](https://github.com/newup-playground/workbench) | Recreates Laravel 4's workbench functionality using NewUp. |

It is easy to install any of the playground templates. To install a playground package template when testing [`newup/core`](https://github.com/newup/core) use the following command:

~~~
php newup.php template:install <playground-name>
~~~

To install a playground package template when testing [`newup/newup`](https://github.com/newup/newup) issue the following command instead:

~~~
php newup template:install <playground-name>
~~~

## License

The NewUp Core is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).