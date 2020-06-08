#!/bin/sh
set -e

chmod 0777 /var/run/docker.sock

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
        set -- apache2-foreground "$@"
fi

exec "$@"