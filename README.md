# Skeleton

## Create the scaffold

```bash

mkdir -p app/config \
  resources/views \
  resources/logs \
  src/Themis \
  public

```
## Import the CSV

In your environment you need to have bash and curl.
In order to import the CSV execute:

```bash
$ bash app/loader.sh ~/Documents/01-ListaMovimenti.csv
.......................................................................................................................................................
Total POST executed: 151
 - with response 201: 151
 - with response 200: 0
 - with response WARNING: 0
```

Because this script now handle Cariparma and also BancaIntesa, it is possible derail the URL from BancaIntesa to Cariparma with `--old` flag (Cariparma is the old API)

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

## Projection

### Project a projection

Inside the container execute:
```bash
/themis $ php scripts/console.php projection:household-budget 2016-03-01 2016-04-01
```
