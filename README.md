This project it's a simple api

The base of this application it's a mini framework using resources as dependency injection and ORM.
This is a very sample version of some technique.

## Dependecies
- PHP 7.4
- Composer
- SQlite3
- PDO

## How to run

After clone the project run `composer install`
Now on all scripts must me configured
Than you can run `composer start` to open the server

## Routes

#### GET - List all users

`http://localhost:8000/users`

##### Extra params
You can use a query string to moving between pages.
`http://localhost:8000/users?page=3`

#### GET - List a user by id

`http://localhost:8000/user/{id}`

for example
`http://localhost:8000/user/3`

#### POST - Add a user

`http://localhost:8000/create_user`

####payload

`
{
    "name":"Vinicius Rangel",
    "lat": -23.529780,
    "long":-46.571040
}
`

##Tests

Run `composer test`
