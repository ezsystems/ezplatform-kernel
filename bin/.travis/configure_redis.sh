#!/bin/sh

echo 'extension = redis.so' >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# Configure redis to work in memory mode and avoid running out of memory
redis-cli config set appendfsync "no"
redis-cli config set maxmemory "60mb"
# commented out to detect if a test uses more then max memory or if clearing is not done correctly between tests
#redis-cli config set maxmemory-policy "allkeys-lru"
redis-cli config set save ""
