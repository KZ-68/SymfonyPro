#!/bin/sh

set -e

echo "🔧 Génération dynamique du filtre Fail2ban 'frankenphp'..."

if [ ! -f /data/filter.d/frankenphp.conf ]; then
cat <<EOF > /data/filter.d/frankenphp.conf
[Definition]
failregex = ^<HOST> - \[".*?"\] - "Go%%20away%%20to%%20my%%20form%%2C%%20bot%%20%%21"
ignoreregex =
datepattern = %%a, %%d %%b %%Y %%H:%%M:%%S %%Z
EOF
else
echo "Le filtre 'frankenphp.conf' est déjà présent."
fi

if [ ! -f /data/jail.d/frankenphp.local ]; then
cat <<EOF > /data/jail.d/frankenphp.local
[frankenphp]
enabled   = true
filter    = frankenphp
logpath   = /var/log/frankenphp/access.log
port      = http,https
findtime = 10m
bantime   = 60000000
protocol = tcp
chain = DOCKER-USER
maxretry  = 3
action = docker-user-ban[banfile_path="/etc/caddy/banned-ips"]
EOF
else
echo "Jail 'frankenphp.local' est déjà présent."
fi

if [ ! -f /data/action.d/docker-user-ban.conf ]; then
cat <<EOF > /data/action.d/docker-user-ban.conf
# Fail2Ban configuration file for caddy banning
#
# Author: Florian Ruechel
#
# To use 'caddy-banfile' action you need to use the caddy-fail2ban extension.
# It can be found at https://github.com/Javex/caddy-fail2ban
# Install and configure it like this:
# @banned {
#   fail2ban ./banfile
# }
# handle @banned {
#   abort
# }
#
# Follow the README in the repo for more information.

[Definition]

# Path where to store banned IPs
banfile_path = /etc/caddy/banned-ips

# Action definition:

actionstart_on_demand = false
actionstart = touch '%(banfile_path)s'

actionflush = truncate -s 0 '%(banfile_path)s'

actionstop = %(actionflush)s

actioncheck = 

_echo_blck_row = printf '%%s\n' "<fid>"

actionban = %(_echo_blck_row)s >> '%(banfile_path)s'

actionunban = id=$"(%(_echo_blck_row)s | sed -e 's/[]\/$*.^|[]/\\&/g')"; sed -i "/^$id$/d" %(banfile_path)s

[Init]
name = docker-user-ban
EOF
else
echo "Config de ban 'docker-user-ban.conf' déjà présent."
fi

echo "✅ Filtre 'frankenphp' généré."

mkdir -p /etc/fail2ban/jail.d

cp /data/filter.d/*.conf /etc/fail2ban/filter.d/
cp /data/jail.d/*.local /etc/fail2ban/jail.d/
cp /data/action.d/*.conf /etc/fail2ban/action.d

exec fail2ban-server -f