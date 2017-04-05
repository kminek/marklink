# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vbguest.auto_update = false

    config.vm.box = "ubuntu/xenial64"

    config.vm.box_check_update = false

    config.vm.network "private_network", ip: "192.168.10.10"

    config.vm.provision "shell", inline: <<-SHELL
        add-apt-repository ppa:ondrej/php
        apt-get update
        apt-get install -y \
        curl \
        unzip \
        php7.1-cli \
        php7.1-dom \
        php7.1-mbstring
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
        echo "cd /vagrant" >> /home/ubuntu/.bashrc
    SHELL

    config.vm.provision "shell", run: "always", inline: <<-SHELL
        cd /vagrant && php -S 0.0.0.0:80 1> /dev/null 2> /dev/null &
    SHELL

end
