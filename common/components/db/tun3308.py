#!/usr/bin/python
# 
# bind ssh tunnel to MySQL server via another remote server
# localhost:3308 - remotehost - mysqlserver:3306
#
# $URL: https://tarax.toyouke.com/svn/MALL/common/components/db/tun3308.py $
# $Id: tun3308.py 1320 2015-08-20 05:01:17Z mori $

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
        ('ec1.homoeopathy.co.jp', 22),
        ssh_username=username,
        ssh_password=password,
        remote_bind_address=('10.51.1.133', 3306),
        local_bind_address=('127.0.0.1', 3308)
        ) as server:

    print(server.local_bind_port)
    while True:
        # press Ctrl-C for stopping
        sleep(1)

print('FINISH!')
