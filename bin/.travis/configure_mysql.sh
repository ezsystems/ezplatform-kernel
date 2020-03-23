#!/bin/sh

# https://github.com/travis-ci/travis-ci/issues/3049
# make sure we don't run out of entropy apparently (see link above)
sudo apt-get -y install haveged
sudo service haveged start
# make tmpfs and run MySQL on it for reasonable performance
sudo mkdir /mnt/ramdisk
sudo mount -t tmpfs -o size=1024m tmpfs /mnt/ramdisk
sudo /etc/init.d/mysql stop
sudo mv /var/lib/mysql /mnt/ramdisk
sudo ln -s /mnt/ramdisk/mysql /var/lib/mysql
sudo /etc/init.d/mysql start
# Install test db
mysql -e "CREATE DATABASE IF NOT EXISTS testdb DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;" -uroot
