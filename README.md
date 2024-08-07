# bukku-laravel
A Bukku Coding assignment. A REST API, for purchase and sale records, made in Laravel.

## Core Requirements
- [x] Basic user register / login capabilities powered by JWT authentication.
- [x] A way to record new purchase transaction.
- [x] A way to retrieve a list of purchase transactions.
- [x] A way to record new sale transaction.
- [x] A way to retrieve a list of sale transactions, with costing information.

## Bonus Requirements
- [x] Allowing creation of transactions in random date order and system to automatically adjust other transactions accordingly.
- [x] Allowing updating / deleting existing transactions.

## APIs Overview
| API Path               | Request Type | Description                      |
|------------------------|--------------|----------------------------------|
| /api/register          | POST         | Registers a user.                |
| /api/login             | POST         | Logs a user in.                  |
| /api/purchase          | POST         | Creates a purchase transaction.  |
| /api/sale              | POST         | Creates a sale transaction.      |
| /api/transactions/{id} | PUT          | Edits an existing transaction.   |
| /api/transactions/:id  | DELETE       | Deletes an existing transaction. |
| /api/sales             | GET          | Gets all sales transactions.     |
| /api/purchases         | GET          | Gets all purchases transactions. |

A simple Postman Collection for your reference : [Here](https://www.postman.com/material-geologist-41281050/workspace/bukku-api-postman/request/14471425-2091f09d-3590-4ac5-906e-69c222ba4b8c).

## Set Up
### Requirements
- PHP 8.x and above
- Composer
- MySQL
- Laravel Herd (Optional)

### Installation
1. Clone the project
```bash
git clone https://github.com/roshenmaghhan/bukku-laravel.git
cd bukku-laravel
```
2. Install dependencies
```bash
composer install
```
3. Create environment variables (Refer to env.example)
> *NOTE* : Do ensure you set up a separate testing database as well, to run testcases, and prevent data being overriden.
4. Run Migrations
```bash
php artisan migrate
```
5. (Optional) Run Product Seeders
```bash
php artisan db:seed --class=ProductSeeder 
```
6. Run the application
```bash
php artisan serve
```

## Tests
Tests are covered for all endpoints, and a unit test is done for the [Weighted Average Cost](https://en.wikipedia.org/wiki/Average_cost_method#:~:text=Weighted%20average%20cost,-Weighted%20average%20cost&text=It%20takes%20cost%20of%20goods,weighted%20average%20cost%20per%20unit.) algorithm.

To run the tests : 
```bash
php artisan test
```
## Seeders
Seeders are created only for the product table, and for 10 entries in total. The seeders are created using a ProductFactory. To run the seeder, run the following command : 
```bash
php artisan db:seed --class=ProductSeeder 
```

## Future Enhancements
Just for the purpose of what could be done with more time : 
- Add filters on GET requests (sort by value, filter by product id etc.).
- Optimize requests (Requires more time with Laravel to know better implementations and methods).
- Weighted Average Costs can be done more granular and consistent, by standardizing a Math.round, since floats can be tricky.