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

You can go to {yourbaseurl}/docs to check the documentation

### V1 Routes

#### Profiles

-   get:profiles (protected)
-   get:profiles/:id (protected)
-   post:profiles (protected)
-   put:profiles/:id (protected)
-   delete:profiles/:id (protected)

#### Projects

-   get:profiles (protected)
    -   ?page=1 (Pagination)
    -   ?match=1 (return a list of project that match with user favorite tags)
-   get:projects/:id (protected)
-   get:profiles/:profile/profiles (protected)
-   get:profiles/:profile/projects/:id (protected)
-   post:profiles/:profile/projects (protected)
-   put:profiles/:profile/projects/:id (protected)
-   delete:profiles/:profile/projects/:id (protected)

#### Tags

-   get:tags

#### Authentication

-   post:login
-   post:register
-   get:logout (protected)
-   get:me (protected)

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
