#!/usr/bin/env bash

current_path=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

if [[ $1 = "dev" ]]
then
  	db_name="simpleapi_db"
elif [[ $1 = "test" ]]
then
  	db_name="simpleapi_db_test"
else
  	echo 'Missing environment! Run with "init-db.sh <env>"'
  	exit 1
fi

echo "Running init-db.sh for ${db_name}..."

# Drop all processes connected to the database
docker exec -e PGPASSWORD=simpleapi_pass -i simpleapi-db \
    psql -U simpleapi_user -h simpleapi-postgres -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '${db_name}' AND pid <> pg_backend_pid();"

# Drop database if it existed
docker exec -e PGPASSWORD=simpleapi_pass -i simpleapi-db \
    psql -U simpleapi_user -h simpleapi-postgres -d postgres -c "DROP DATABASE IF EXISTS ${db_name};"
if [[ $? -ne 0 ]]
then
    echo "Cannot drop database ${db_name}. Aborting..."
    exit 2
fi

# Create database
docker exec -e PGPASSWORD=simpleapi_pass -i simpleapi-db \
    psql -U simpleapi_user -h simpleapi-postgres -d postgres -c "CREATE DATABASE ${db_name};"
if [[ $? -ne 0 ]]
then
    echo "Failed creating database ${db_name}."
    exit 2
fi

# Import database dump
docker exec -e PGPASSWORD=simpleapi_pass -i simpleapi-db \
    psql -U simpleapi_user -h simpleapi-postgres -d ${db_name} < ${current_path}/db_skeleton.sql
if [[ $? -ne 0 ]]
then
    echo "Failed importing database dump to ${db_name}."
    exit 2
fi

# Run fixtures for test environment
if [[ $1 = "test" ]]
then
    echo "Running fixtures..."
	docker exec simpleapi-php-fpm php /application/tests/run_fixtures.php
    if [[ $? -ne 0 ]]
    then
        echo "Failed running fixtures."
        exit 2
    fi
    echo "Fixtures done."
fi
