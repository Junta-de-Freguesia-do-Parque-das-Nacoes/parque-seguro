#!/bin/bash

#docker run -v docker start mysql
# docker run --name snipe-mysql -e MYSQL_ROOT_PASSWORD=my_crazy_super_secret_root_password -e MYSQL_DATABASE=snipeit -e MYSQL_USER=snipeit -e MYSQL_PASSWORD=whateverdood -d mysql
docker run -d snipe-mysql
#docker run -d -v ~/Documents/snipeyhead/Parque Seguro/:/var/www/html -p $(boot2docker ip)::80   --link snipe-mysql:mysql --name=snipeit snipeit
docker run --link snipe-mysql:mysql -d -p 40000:80 --name=Parque Seguro -v ~/Documents/snipeyhead/Parque Seguro/:/var/www/html \
-v ~/Documents/snipeyhead/Parque Seguro-storage:/var/lib/snipeit --env-file docker.env snipe-test
