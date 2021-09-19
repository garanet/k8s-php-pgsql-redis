#!/bin/bash
# php+pgsql+redis project - DEPLOY SCRIPT
# @maintainer G.Gatto 2021 - www.garanet.net
# repo from https://github.com/garanet/k8s-php-pgsql-redis
# Tested on a MacOsx with Docker + Kuberneters (Docker-Desktop)

if [ "$1" == "start" ]; then
    clear   
    echo "+ Building the custom PHP-FPM NGINX image"
    if docker build -t php ./php/build/ ; then 
        echo '+ Deploying the custom PHP-FPM NGINX image'
        kubectl apply -f ./php/.
        sleep 5
        echo ''
        echo '+ Deploying the POSTGRESQL image'
        kubectl apply -f ./postgresql/.
        sleep 5
        echo ''
        echo '+ Deploying the REDIS image'
        kubectl apply -f ./redis/.
        sleep 5
        echo ''
        echo '+ Exposing the POD to localhost:8000'
        sleep 10
        kubectl port-forward deployment/php-fpm-nginx 8000:80
        echo ''
        echo '+ Open your browser and visit http://localhost:8000'
        echo ''
    else
        clear
        echo "!!!"
        echo '+ Exception to build the custom PHP-FPM NGINX image, please check your ENV!!!'
        echo "!!!"
    fi
else
    if [ "$1" == "stop" ]; then
        clear
        kubectl delete -f ./php/.
        kubectl delete -f ./postgresql/.
        kubectl delete -f ./redis/.
        echo "+ DONE !!!"
    else
        clear
        echo '!!! Please use the script with arguments start / stop:'
        echo '--- Start Pods=> #: ./deploy.sh start'
        echo '-'
        echo '--- Stop Pods=> #: ./deploy.sh stop' 
        exit 0
    fi
fi