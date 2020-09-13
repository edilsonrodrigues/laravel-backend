# laravel backend template

## Config files (make it before run the application)
#### Add your settings to these files:
> .env

## Install!
```
composer install
```

## Run it!

```
php artisan serve
```

## Request examples

Open [http://127.0.0.1:8000](http://127.0.0.1:8000)

Sing up Example [POST]:
[http://127.0.0.1:8000/api/authenticate](http://127.0.0.1:8000/api/authenticate)
body:
`{
    "user":{"id":1,"name":"edilson"},
    "token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxIiwibmFtZSI6IkVkaWxzb24iLCJpYXQiOjE1MTYyMzkwMjJ9.7CqLmtai6IGDmW_R0FcNtgl43lhazAZjsgNjJB5wCPA"
}`
Sing up Example [GET]:
[http://127.0.0.1:8000/api/person](http://127.0.0.1:8000/api/person)
Headers { "Authorization":"<Token recived on the previous request>", "Content-type":"application/json" }
body:
`{
    "uuid":"1",
    "name":"Edilson"
}`