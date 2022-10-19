
# Amo Api Laravel Test Project 



## Installation

clone project
```bash
  cd my-project

  composer update && composer install

  config .env

  php artisan migrate
```

test amo acount data 

AMO_CLIENT_ID=e755801c-5892-4b49-8246-b1a461323e74
AMO_CLIENT_SECRET=TOSwSGE8u6iBUulwRkZ5G3ZCTswzwkinjrpqjwJZSITFTp7w1iNZUHibc0m18MJb
AMO_REDIRECT_URI=https://new-city.online/get-token

I used my anoter project whith https support for get token from redirect AMO_REDIRECT_URI

So i have tokens.txt in my root folder  which should't be there,

in real case i would use my storage folder and https server from site for getting the tokent from redirected code

