# Skeleton


## Create the scaffold

```bash

mkdir -p app/config \
  resources/views \
  resources/logs \
  src/Themis \
  public

```

## Docker

### Build the Phpunit container

execute this

```bash
$ docker build --file DockerfilePhpunit -t pasquinis/phpunit:5.7 .
```
### Run tests

for execute the tests runs:
```bash
/themis $ composer install
/themis $ phpunit
```

### Start the container

with:
- mount host directory (read/write) with container directory `/themis`
- expose external port 9000 with container port 8080

```bash
$ docker run -it -u 1000:1000 -v $(pwd):/themis:rw pasquinis/phpunit:5.7 sh
```

### Create SQLite schema

for the first setup execute inside the container

```bash
/themis $ sqlite3 app.db < resources/sql/schema.sql
```

### Run the PHP Server

use the docker-compose web service

```bash
$ docker-compose up web
```
