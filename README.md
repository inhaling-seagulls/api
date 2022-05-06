# Project that needs a name

## How to start

### Environnement 
// TODO

### Command line
```
composer install
php artisan migrate
php artisan serve
```

## Use the API

### Routes

#### Profiles
- get:profiles
- get:profiles/:id
- post:profiles
- put:profiles/:id
- delete:profiles/:id
#### Projects
- get:projects/:id
- post:projects
- put:projects/:id
- delete:projects/:id
#### Tags
- get:tags

### Add/Update a profile
Route: post:profiles

Body request format
```
{
    "pseudo": "Pseudo",
    "contact": "Your infos",
    "tags": [
        {
            "tag_id": 1
        }
    ]
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
    "tags": [
        {"tag_id": 1}
    ],
    "profile_id" : 1
}
```
