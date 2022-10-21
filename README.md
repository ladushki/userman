# Userman
Service can be used to create and update a user,
set credentials and check them.
It uses Illuminate\Database to connect and build the queries but not the Eloquent.

## Usage:

```php
$db = Capsule::getConnection();
$hasher = new PasswordHash(1, []);
$repository = new IlluminateUserRepository($db, $hasher);
$userman = new UserManager($repository);

//create
$input = ['name' => 'Larissa', 'surname' => 'Smith', 'email' => 'larissa@test.com', 'password' => 'password'];
$user = $userman->createUser($input);

//update
$inputForUpdate = ['password' => 'password2', 'email' => 'larissa2@test.com',];
$user = $userman->updateUser($inputForUpdate);

//list
$users = $userman->listUsers();
```
### ToDo: 
 - move the validation out
 - better error handling
 - refactor some other parts