# PHP eXtended Enums

The missing quality-of-life features from PHP 8+ Enums.

This library adds a few useful traits to your PHP Enums that compliment existing builtins.

For example,

```php
use \victorwesterlund\xEnum;

enum HelloWorld: string {
    use xEnum;

    case FOO = "BAR";
    case BAZ = "QUX";
}

// Like Enum::from() but for Enum names instead of values
HelloWorld::fromName("FOO"); // HelloWorld::FOO
// And of course the non-throwing version similar to Enum::tryFrom()
HelloWorld::tryFromName("MOM"); // null
```

# How to use

Requires PHP 8.0 or newer

1. **Install from composer**
    ```
    composer require victorwesterlund/xenum
    ```

2. **`use` in your project**
    ```php
    use \victorwesterlund\xEnum;
    ```

3. **`use` with your Enums**
    ```php
    enum HelloWorld {
        use xEnum;
    }
    ```


# Methods

All methods implemented by this library

Method
--|
[Enum::fromName(int\|string\|null): static](#enumfromname)
[Enum::tryFromName(int\|string\|null): static\|null](#enumtryfromname)
[Enum::names(): array](#enumnames)
[Enum::values(): array](#enumvalues)
[Enum::entries(): array](#enumentries)

## Enum::fromName()

Resolve an Enum case from case name or throw `ValueError` if case not found.

Similar to: [Enum::from()](https://www.php.net/manual/en/language.enumerations.backed.php)

```php
Enum::fromName(int|string|null): static
```

Example:

```php
enum HelloWorld: string {
    use xEnum;
    
    case FOO = "BAR";
    case BAZ = "QUX";
}

HelloWorld::fromName("FOO"); // HelloWorld::FOO
HelloWorld::fromName("MOM") // ValueError: 'MOM' is not a valid case for HelloWorld
```

## Enum::tryFromName()

Resolve an Enum case from case name or return `null` if no match found

Similar to: [Enum::tryFrom()](https://www.php.net/manual/en/language.enumerations.backed.php)

```php
Enum::tryFromName(int|string|null): static|null
```

Example:

```php
enum HelloWorld: string {
    use xEnum;
    
    case FOO = "BAR";
    case BAZ = "QUX";
}

HelloWorld::tryFromName("FOO"); // HelloWorld::FOO
HelloWorld::tryFromName("MOM") // null
```

## Enum::names()

Return sequential array of Enum case names

```php
Enum::names(): array
```

Example:

```php
enum HelloWorld: string {
    use xEnum;
    
    case FOO = "BAR";
    case BAZ = "QUX";
}

HelloWorld::names(); // ["FOO", "BAZ"]
```

## Enum::values()

Return sequential array of Enum case values

```php
Enum::entries(): array
```

Example:

```php
enum HelloWorld: string {
    use xEnum;
    
    case FOO = "BAR";
    case BAZ = "QUX";
}

HelloWorld::values(); // ["BAR", "QUX"]
```

## Enum::entries()

Return an associative array of Enum names and values. This method is similar to [JavaScript's Object.entries()](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/entries)

```php
Enum::entries(): array
```

Example:

```php
enum HelloWorld: string {
    use xEnum;
    
    case FOO = "BAR";
    case BAZ = "QUX";
}

HelloWorld::entries(); // ["FOO" => "BAR", "BAZ" => "QUX"]
```
