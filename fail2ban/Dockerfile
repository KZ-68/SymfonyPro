FROM crazymax/fail2ban:edge AS fail2ban_upstream

# Base fail2ban image
FROM fail2ban_upstream AS fail2ban_base

COPY fail2ban/usr/local/bin/custom-init.sh /usr/local/bin

ENTRYPOINT ["custom-init.sh"]