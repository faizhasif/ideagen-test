## Ideagen IV Test

This project assumes Docker is installed on your machine

Navigate to the project root folder on your machine and run the following command

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

This will install all PHP dependencies without having PHP binaries installed on your machine


## Initializing Project

### Project Key
After the dependencies are installed, run the following command to create a new environment variables file for the project.

```
cp .env.example .env
```

Please make sure to furnish the `DB_PASSWORD` in the `.env` file before continuing below.

## Running Project
Run the following command to start the project

```
./vendor/bin/sail up -d
```
### Project Key
Run the following command to furnish a unique key for the project

```
./vendor/bin/sail artisan key:generate
```

This will fill up the `APP_KEY` in the `.env` file.

### Database Migration
Run the following command to migrate and seed the database.

```
./vendor/bin/sail artisan migrate --seed
```

## Interacting with the Project
In your browser, navigate to `localhost` to verify that the server for this project is up and running correctly.

The exposed API can be accessed at the following URL

Product Listing - `localhost/api/products`

The above endpoint accept the following optional query parameters
- `name`
- `category`
- `min_price`
- `max_price`

## Testing the Project
While the container is running, run the following command
```
./vendor/bin/sail artisan test
```

## Closing Project
Run the following command to close the project
```
./vendor/bin/sail down
```
