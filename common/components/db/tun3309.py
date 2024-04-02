#!/usr/bin/python
# 
# bind ssh tunnel to MySQL server via another remote server
# localhost:3309 - remotehost - mysqlserver:3306
#
# $URL: https://tarax.toyouke.com/svn/MALL/common/components/db/tun3309.py $
# $Id: tun3309.py 1318 2015-08-20 04:23:21Z mori $

from sshtunnel import SSHTunnelForwarder
from time import sleep
from sys import argv

username = ''
password = ''
try:
    username = argv[1]
    password = argv[2]
except  IndexError:
    pass

with SSHTunnelForwarder(
        ('shop.toyouke.com', 22),
        ssh_username=username,
        ssh_password=password,
        remote_bind_address=('10.51.1.55', 3306),
        local_bind_address=('127.0.0.1', 3309)
        ) as server:

    print(server.local_bind_port)
    while True:
        # press Ctrl-C for stopping
        sleep(1)

print('FINISH!')

