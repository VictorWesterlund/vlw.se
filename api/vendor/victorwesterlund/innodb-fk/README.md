# php-libinnodb-fk

This library retrievies and optionally resolves foreign keys in a MySQL/MariaDB database using the InnoDB storage engine.

**This library will only work with databases created with InnoDB**

## Install with composer
```
composer require victorwesterlund/innodb-fk
```
```php
use victorwesterlund\ForeignKeys
```

# Example / Documentation

Start by initializing `ForeignKeys` with `mysqli` connection details. `ForeignKeys` will pass the arguments along to `mysqli::__construct()`.

Remember to pass the database name where InnoDB foreign keys are stored to the 4th argument. The user must also have `SELECT` permissions on this database as the (4th) `$database` argument. It's usually `information_schema`. You can also pass the `ForeignKeys::DATABASE_NAME` constant if you're unsure.

**Example database relationship:**

![image](https://github.com/VictorWesterlund/php-libinnodb-fk/assets/35688133/f3efedc2-6b61-4ca5-a55e-59da78c3e9ee)

## Initialize `ForeignKeys`

```php
use victorwesterlund\ForeignKeys

$fk = new ForeignKeys($host, $user, $pass, ForeignKeys::DATABASE_NAME);
```

## Get column constraints for a table

Pass a database and table to `for()` and then chain `get_constraints()` to receive an associative array of all column relationships for that table.

```php
$fk->for("test", "bar")->get_constraints();
```
```php
[
   // Name of the column that has a foreign key reference
   "fk" => [
      // key is the database and table it references. Value is the column
      "test.foo" => "id"
   ]
]
```

## Resolve foreign key references for entities

You can also resolve foreign key references for a passed array of arrays.

Retrieve rows from your database and pass them to `resolve_all()` as an array of associatve arrays to resolve them automatically.

```php
$rows = [
   [
      "id" => 1,
      "fk" => 2
   ],
   [
      "id" => 2,
      "fk" => 1
   ]
];

$rows = $fk->for("test", "bar")->resolve_all($rows);
```
```php
// $rows will become
[
   [
      "id" => 1,
      "fk" => [
         "id"    => 2,
         "value" => "lorem ipsum dolor sit amet"
      ]
   ],
   [
      "id" => 2,
      "fk" => [
         "id"    => 1,
         "value" => "consectetur adipiscing elit"
      ]
   ]
]
```
