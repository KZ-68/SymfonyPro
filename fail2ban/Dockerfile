FROM crazymax/fail2ban:edge AS fail2ban_upstream

# Base fail2ban image
FROM fail2ban_upstream AS fail2ban_base

COPY --link --chmod=755 fail2ban/usr/local/bin/custom-init.sh /usr/local/bin/custom-init.sh