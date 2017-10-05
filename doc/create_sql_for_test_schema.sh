#!/usr/bin/env bash

if test -e "app_test.sql"; then
    rm -f app_test.sql
fi

if test -e "app.sql"; then
    cp app.sql app_test.sql
    sed -i -e 's/app/app_test/g' app_test.sql
fi
