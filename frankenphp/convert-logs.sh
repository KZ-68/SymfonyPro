#!/bin/sh
touch /var/log/frankenphp/access_text.log
tail -F /var/log/frankenphp/access.log | \
jq -r '"\(.request.client_ip) - \(.request.method) \(.request.uri)"' >> /var/log/frankenphp/access_text.log