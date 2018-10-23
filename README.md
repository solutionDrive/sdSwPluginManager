Shopware plugin manager
=======================

This is a CLI based plugin manager that can simply install, uninstall, enable or disable plugins in a shopware shop.
The shopware instance needs not to be fully working for this plugin manager
to work as it is designed to be independent from the Shopware core itself.

The `StoreApiProvider` is heavily inspired by `store-plugin-installer` from `shyim` see [here](https://github.com/shyim/store-plugin-installer)

Install
-------

The recommended way to install this package manager is to require it via composer:

    composer require solutiondrive/sdswpluginmanager


Alternatively, you can just download a release and unzip it.


Usage as a manual package manager
----------------------------------

This package manager can be used as a (not feature complete) drop-in replacement for Shopware's console plugin commands:

    bin/sd-plugin-manager sd:plugins:install
    bin/sd-plugin-manager sd:plugins:activate
    bin/sd-plugin-manager sd:plugins:deactivate
    bin/sd-plugin-manager sd:plugins:uninstall


Usage as an automatic package manager
-------------------------------------

But the more important feature is the automatic package management.
You can provide a configuration file containing the plugins to be installed in the current shop.
Then this package manager will ensure that the shop contains **exactly** the specified plugins in the specified version.

You can see an example for such a configuration in ```etc/examples/example_config.yml```.

    bin/sd-plugin-manager sd:plugins:deploy:auto --env=dev etc/examples/example_config.yml

Develop
-------

To create the phar, you need to install ```kherge/box``` globally:
(The global composer bin path needs to be available in $PATH)

    composer global require kherge/box

Install the dependencies

    composer install --optimize-autoloader -n

Create the phar file

    box build -c box.json
    
Perhaps it is necessary to allow php to create a phar

    php -d phar.readonly=0 ~/.composer/vendor/bin/box build -c box.json

The newly created phar file can be used just like this:

    build/sd-sw-plugin-manager.phar --help


License
-------

MIT. See file /LICENSE .
