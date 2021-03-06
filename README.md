# LogHero PHP SDK

## Developer Setup

Install PHP:
```
sudo dnf install php-cli php-json
```

Download the composer-setup script https://getcomposer.org/download/ and run:

```
php composer-setup.php --install-dir /home/user/.local/bin/ --filename=composer
```

Install the required PHP extensions and run 'composer install' to load the PHP dependencies:

```
sudo dnf install php-mbstring php-xml php-posix
composer install
```

## Starting VM with Sample Page

Install [Composer](https://getcomposer.org/doc/00-intro.md#locally), [VirtualBox](https://www.virtualbox.org/), [Vagrant](https://www.vagrantup.com/) and [Ansible](http://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html).
Checkout the repository, install the dependencies with composer and start the virtual machine:
```
git clone git@github.com:LogHero/lh-php-sdk.git
vagrant ssh -c 'cd /var/www/default/htdocs/sdk && composer install --no-dev'
cd lh-php-sdk/
vagrant up
```
Vagrant will add a static IP address to the VM.
To access the sample site, add the following line to your hosts file:
```
192.168.1.11    sdk.loghero.io
```
Now you can access the sample site: http://sdk.loghero.io/sdk/sample/hello-world/index.php

If the server is having trouble to write the logs to the buffer file, make sure it has write permissions on the ```sample/hello-world``` folder.