# Profiles Contacts Seeder 

> Laravel application for seeding contact and profile details to Trengo server.


**Acceptance criteria:**
1. Setup a Trengo account
   1. Create a Trengo demo account at https://trengo.com/en/register/
   2. Generate an API token in “Settings -> Apps & integrations -> Rest API”
   
2. Create profiles and contacts in Trengo using the two source files (`companies.csv` and `contacts.csv` in this repository)
   1. Companies should create unique profiles, and contacts need to be attached to those profiles 
   2. Endpoints to use:
      1. Create a profile: https://developers.trengo.com/reference/create-a-profile
      2. Create contact: https://developers.trengo.com/reference/create-update-a-user
      3. Attach a contact to a profile: https://developers.trengo.com/reference/attach-a-contact
   3. Nice to have: Populating custom contact fields


### Clone

- Clone the repository using `git clone https://github.com/tosinibrahim96/profile-contact-seeder.git`
- Create a `.env` file in the root folder and copy everything from `.env-sample` into it
- Fill the `.env` values with your Database details as required


### Setup

- Download WAMP or XAMPP to manage APACHE, MYSQL and PhpMyAdmin. This also installs PHP by default. You can follow [this ](https://youtu.be/h6DEDm7C37A)tutorial
- Download and install [composer ](https://getcomposer.org/)globally on your system

> install all project dependencies and generate application key

```shell
$ composer install
$ php artisan key:generate
```
> migrate all tables and seed required data into the database

```shell
$ php artisan migrate:fresh --seed
```
> start your Apache server and MySQL on WAMP or XAMPP interface
> serve your project using the default laravel PORT or manually specify a PORT

```shell
$ php artisan serve (Default PORT)
$ php artisan serve --port={PORT_NUMBER} (setting a PORT manually)
```

### License

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright 2022 © <a href="https://tosinibrahim96.github.io/Resume/" target="_blank">Ibrahim Alausa</a>.
