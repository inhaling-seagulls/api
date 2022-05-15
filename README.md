# Project that needs a name

## How to start

### Environnement

Copy .env.example file and rename it .env

Put your Database parameters.

Example :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={YOUR_DB_NAME}
DB_USERNAME={YOUR_USER_NAME}
DB_PASSWORD={YOUR_PASSWORD}
```

### Command line

```
composer install
php artisan db:create
php artisan migrate
php artisan serve
```

## Use the API

### Routes

#### Profiles

-   get:profiles/:id
-   post:profiles
-   put:profiles/:id
-   delete:profiles/:id

#### Projects

-   get:profiles
    -   ?page=1 (Pagination)
    -   ?match (WIP : return a list of project that match with user favorite tags)
-   get:projects/:id
-   post:projects
-   put:projects/:id
-   delete:projects/:id

#### Tags

-   get:tags

### Add/Update a profile

Route: post:profiles

Body request format

```
{
    "pseudo": "Pseudo",
    "contact": "Your infos",
    "tags": [ 1, 2 ]
}
```

### Add/Update a project

Route: post:projects

Body request format

```
{
    "name": "project name",
    "description": "description",
    "image": "url",
    "tags": [ 1, 2 ]
}
```
