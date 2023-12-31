# <p align="center">Code challange for developer. <br>Excel upload using Laravel Vue.</p>


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Code Challange

Please write a program that able to upload students’ data using Excel and create the students record if there are no record yet. If has record, skip it and proceed it with the next row.
The record should contain data as below:

<p align="center"><img src="https://github.com/arr2504/excel-upload/blob/main/storage/app/public/printscreen-code-challange.png?raw=true"></p>

## Introduction

This project have function like :

- Login page
- Register page
- Home page (In here user able to upload file excel and see the table result)

## I'am code using

This code challenge was created using Laravel, Vue.js, and the Vuez plugin, which sets up the Vue environment in a Laravel project.

- Laravel 10
- Vue.js
- <a href="https://github.com/arr2504/vuez">Vuez</a>
- <a href="https://github.com/SpartnerNL/Laravel-Excel">Maatwebsite/excel</a>

"Maatwebsite/excel" package to enable easy Excel file upload and processing. With this package, developers can effortlessly handle Excel files in their Laravel applications, making it convenient for users to upload and work with Excel data.


## Installation

Install all required plugin

```
composer install
```

Migration database

```
php artisan migrate
```

Run project on the local
```
php artisan serve
php artisan storage:link
```
```
npm run dev 
```

Run project on the server
```
php artisan storage:link
npm run build 
```

## The Result

Login page : 
<img src="https://github.com/arr2504/excel-upload/blob/main/storage/app/public/login.png?raw=true">

Register page : 
<img src="https://github.com/arr2504/excel-upload/blob/main/storage/app/public/register.png?raw=true">

Home page : 
<img src="https://github.com/arr2504/excel-upload/blob/main/storage/app/public/home.png?raw=true">

