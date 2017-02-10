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

### Start the container

with:
- mount host directory (read/write) with container directory `/themis`
- expose external port 9000 with container port 8080

```bash
$ docker run -it -u 1000:1000 -v $(pwd):/themis:rw -p 9000:8080 pasquinis/phpunit:5.7 sh
```

### Run the PHP Server

```bash
$ php -S 0.0.0.0:8080 -t /themis/public/ /themis/public/index.php
```