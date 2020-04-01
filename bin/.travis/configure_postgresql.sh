#!/bin/sh

sudo mount -o remount,size=25% /var/ramfs;
psql -c "CREATE DATABASE testdb;" -U postgres;
psql -c "CREATE EXTENSION pgcrypto;" -U postgres testdb
