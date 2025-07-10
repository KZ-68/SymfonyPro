#!/bin/sh
set -e

echo "🔧 Génération dynamique du filtre Fail2ban 'frankenphp'..."

if [ ! -f /data/filter.d/frankenphp.conf ]; then
cat <<EOF > /data/filter.d/frankenphp.conf
[Definition]
failregex = ^<HOST> - Go away to my form, bot !$
ignoreregex =
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
banaction = iptables-multiport
maxretry  = 3
EOF
else
echo "Jail 'frankenphp.local' est déjà présent."
fi

echo "✅ Filtre 'frankenphp' généré."

mkdir -p /etc/fail2ban/jail.d

cp /data/filter.d/*.conf /etc/fail2ban/filter.d/
cp /data/jail.d/*.local /etc/fail2ban/jail.d/

exec fail2ban-server -f