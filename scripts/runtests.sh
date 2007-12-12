#!/bin/bash
# Runs functional tests
# $Id: runtests.sh 960 2007-07-19 14:14:04Z alex $

cd ..
list=(documentsActions summitsActions usersActions)

for i in ${list[@]}; do
    echo test: $i
    echo ===========
    symfony test-functional frontend $i
    echo
done
