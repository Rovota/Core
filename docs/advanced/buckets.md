---
description: 'Namespace: Rovota\Core\Support'
---

# Buckets

### Introduction

Buckets are used throughout Core in various places. They provide ease of use when working with structured data, like configuration, request input and more. While they aren't as powerful as [collections](collections.md) in terms of functionality, there are still benefits to using Buckets instead.

Unlike collections, buckets are not suitable to store a "collection" of objects and they can be used to work with arrays using the [dot notation](buckets.md#dot-notation).

### Compatibility

The Bucket class implements `ArrayAccess` and `JsonSerializable`. This makes it possible to use buckets  as if they were arrays in many places, with the exception of parameters that require a value of type `array`. In these cases, you can use the `toArray()` method.

### Dependencies

The [dflydev](https://github.com/dflydev)/[**dflydev-dot-access-data**](https://github.com/dflydev/dflydev-dot-access-data) **** dependency is used.

### Creation

Bucket objects can be created and obtained through various ways. Often, you'll work with buckets through various functionality throughout Core, like [requests](../basics/requests.md), [validation](../security/validation.md) and [localization](localization.md).

You can also create buckets yourself by instantiating one like so:

```php
use Rovota\Core\Support\Bucket;

$bucket = new Bucket();
```

### Dot Notation

In certain scenarios, accessing nested data within arrays may be difficult, like when you get the entire array as a return value. To make code more readable and maintainable, you could use the dot notation instead. Let's take the following array as an example:

```php
$person = [
    'name' => 'Steven',
    'preferences' => [
        'color' => 'Magenta',
        'sports' => [
            'outdoors' => 'Snowboarding',
            'indoors' => 'Basketball',
        ],
    ],
];
```

To access the favorite outdoor activity of Steven using the traditional way, we'd do this:

```php
$activity = $person['preferences']['sports']['outdoors'];
// Snowboarding
```

Now, what if this had been a Bucket instead? We would get this:

```php
$activity = $person->get('preferences.sports.outdoors');
// Snowboarding
```

Additionally, we'd be able to make sure the returned value was of a certain type, but also provide a default value for when any of the nested arrays are missing:

```php
$drink = $person->string('preferences.consumables.drink', 'Fanta');
// Fanta, because the array key "consumables" does not exist.
```

### Methods

The following methods are available to make it easy to work with the underlying array. All of these methods modify the original Bucket object.

| [all](buckets.md#all)               | [export](buckets.md#export)       | [int](buckets.md#undefined-3)            | [string](buckets.md#undefined-13)  |
| ----------------------------------- | --------------------------------- | ---------------------------------------- | ---------------------------------- |
| [append](buckets.md#append)         | [filled](buckets.md#filled)       | [merge](buckets.md#undefined-4)          | [toArray](buckets.md#undefined-14) |
| [array](buckets.md#array)           | [filledAny](buckets.md#filledany) | [mergeIfMissing](buckets.md#undefined-5) | [toJson](buckets.md#undefined-15)  |
| [bool](buckets.md#bool)             | [float](buckets.md#float)         | [missing](buckets.md#undefined-6)        |                                    |
| [collect](buckets.md#collect)       | [flush](buckets.md#flush)         | [missingAny](buckets.md#undefined-7)     |                                    |
| [collection](buckets.md#collection) | [get](buckets.md#get)             | [moment](buckets.md#undefined-8)         |                                    |
| [count](buckets.md#count)           | [has](buckets.md#has)             | [only](buckets.md#undefined-9)           |                                    |
| [date](buckets.md#date)             | [hasAll](buckets.md#undefined)    | [remove](buckets.md#undefined-10)        |                                    |
| [enum](buckets.md#enum)             | [hasAny](buckets.md#undefined-1)  | [replace](buckets.md#undefined-11)       |                                    |
| [except](buckets.md#except)         | [import](buckets.md#undefined-2)  | [set](buckets.md#undefined-12)           |                                    |

### Examples

For the purposes of keeping each example concise, we'll use the same Bucket object with all following examples:&#x20;

```php
use Rovota\Core\Support\Bucket;

$bucket = new Bucket([
    'username' => 'Jessica',
    'profile' => [
        'nickname' => 'SpaceRanger45',
        'biography' => 'Flying through life like astronauts fly through space.',
        'hobbies' => ['Tennis', 'Movies', 'Drawing'],
        'gender' => 'F',
        'public' => 1,
    ],
    'progress' => [
        'essentials' => 65,
    ],
    'tags' => 'popular,social,brunette,fit',
    'age' => 26,
    'birthdate' => '1994-09-24',
]);
```

#### `all()`

Returns the array used internally by the bucket:

```php
$bucket->all();
```

{% hint style="info" %}
This method is an alias of [`toArray()`](buckets.md#undefined-14).
{% endhint %}

#### `append()`

Adds a value to the given key. If the key does not yet exist, it will be created. If the key references a non-array value, its value will be put inside an array before appending the new value:

```php
$bucket->append('profile.hobbies', 'Music');
```

#### `array()`

If the value is an array, it'll be returned as-is. However, when the value is a string containing words separated by commas, the string will be returned as an array with each comma-separated word as an array value:

```php
$bucket->array('profile.hobbies');

// ['Tennis', 'Movies', 'Drawing']
```

```php
$bucket->array('tags');

// ['popular', 'social', 'brunette', 'fit']
```

#### `bool()`

Returns the value as a boolean. When the value cannot be converted to boolean, it will return `false`:

```php
$bucket->bool('profile.public');

// true
```

#### `collect()`

Transforms the entire bucket into a [Collection](collections.md) instance:

```php
$bucket->collect();
```

#### `collection()`

Returns the value as a [Collection](collections.md) instance:

```php
$bucket->collection('profile.hobbies');
```

{% hint style="info" %}
&#x20;Internally, it uses [`array()`](buckets.md#array) before creating a collection. This also means that strings with comma-separated words will be split into an array of words.
{% endhint %}

#### `count()`

Returns how many items are present for a given key. When the key doesn't reference an array, it'll return `1`:

```php
$bucket->collection('profile.hobbies');

// 3
```

#### `date()`

Returns the value as a standard DateTime instance. Optionally, you could specify the time zone that should be used as second parameter:

```php
$bucket->date('birthdate');
```

{% hint style="info" %}
This method exists for compatibility reasons. When possible, it is recommended to use [`moment()`](buckets.md#moment) instead, as [Moment](date-and-time.md) objects provide better usability and functionality.
{% endhint %}

#### `enum()`

Returns the value as an Enum case when possible. You have to specify the Enum to use as second parameter:

```php
use Rovota\Core\Support\Enums\Status;

$bucket->enum('profile.public', Status::class);

// Status::Enabled
```

#### `except()`

Returns all keys in the bucket except for those specified:

```php
$bucket->except(['profile', 'age', 'birthdate']);

// [
//     'username' => 'Jessica',
//     'progress' => [
//         'essentials' => 65,
//     ],
// ]
```

#### `export()`

Returns the bucket contents as an array:

```php
$bucket->export();
```

{% hint style="info" %}
This method is an alias of [`toArray()`](buckets.md#undefined-14).
{% endhint %}

#### `filled()`

Checks whether the provided key(s) exists and are filled. Values equal to `null` will be seen as not filled:

```php
$bucket->filled(['graduated', 'age']);

// false
```

#### `filledAny()`

Checks whether one or more the provided keys exists and is filled. Values equal to `null` will be seen as not filled:

```php
$bucket->filledAny(['graduated', 'age']);

// true
```

#### `float()`

Returns the value as a float. When the value cannot be converted to float, it will return `false`:

```php
$bucket->float('progress.esentials');

// 65.0
```

#### `flush()`

Empties the bucket completely:

```php
$bucket->
```

#### `get()`

Returns the value for a given key. If the key does not exist, the default value is returned:

```php
$bucket->get('profile.nickname', 'No nickname available');

// SpaceRanger45
```

#### `has()`

Checks whether a key is present:

```php
$bucket->has('username']);

// true
```

#### `hasAll()`

Checks whether all given keys are present:

```php
$bucket->hasAll(['username', 'age', 'graduation']);

// false
```

#### `hasAny()`

Checks whether at least one of the given keys is present:

```php
$bucket->hasAny(['username', 'age', 'graduation']);

// true
```

#### `import()`

Imports an array of data into the bucket:

```php
$bucket->import([
    'username' => 'Robert',
    'profile' => [
        'gender' => 'M',
    ],
]);
```

{% hint style="info" %}
By default, it behaves like [`replace()`](buckets.md#undefined-11), but you can modify it's behavior by passing either `Bucket::PRESERVE`, `Bucket::REPLACE` or `Bucket::MERGE` as second parameter.
{% endhint %}

#### `int()`

Returns the value as an integer. When the value cannot be converted to integer, it will return `false`:

```php
$bucket->int('age');

// 26
```

#### `merge()`

Merges all provided values with the existing values in the bucket:

```php
$bucket->replace([
    'username' => 'Robert',
    'profile' => [
        'gender' => 'M',
    ],
]);
```

#### `mergeIfMissing()`

Merges only the values that have a key not yet present:

```php
$bucket->replace([
    'username' => 'Robert',
    'profile' => [
        'gender' => 'M',
    ],
]);
```

#### `missing()`

Returns `true` when the given key is missing, and `false` otherwise:

```php
$bucket->missing('graduated');

// true
```

#### `missingAny()`

Returns `true` when any of the given keys are missing, and `false` otherwise:

```php
$bucket->missing(['graduated', 'age']);

// true
```

#### `moment()`

Returns the value as a [Moment](date-and-time.md) instance. Optionally, you could specify the time zone that should be used as second parameter:

```php
$bucket->moment('birthdate', 'Europe/Amsterdam');
```

#### `only()`

Returns an array that contains only the key(s) specified:

```php
$bucket->only(['username', 'age']);

// ['username' => 'Jessica', 'age' => 26]
```

#### `remove()`

Removes a given key from the collection:

```php
$bucket->remove('profile.gender');
```

#### `replace()`

Replaces existing values for each key, with the provided values:

```php
$bucket->replace([
    'username' => 'Robert',
    'profile' => [
        'gender' => 'M',
    ],
]);
```

#### `set()`

Set a value for a given key. If the key does not exist, it will be created:

```php
$bucket->set('graduated', true);
```

#### `string()`

Returns the value as a string, as long as it can be cast:

```php
$bucket->string('profile.public');

// "1"
```

#### `toArray()`

Returns an array representation created from the data in the bucket:

```php
$bucket-toArray();

// [
//     'username' => 'Jessica',
//     'profile' => [
//         'nickname' => 'SpaceRanger45',
//         'biography' => 'Flying through life like astronauts fly through space.',
//         'hobbies' => ['Tennis', 'Movies', 'Drawing'],
//         'gender' => 'F',
//         'public_profile' => 1,
//     ],
//     'progress' => [
//         'essentials' => 65,
//     ],
//     'age' => 26,
//     'birthdate' => '1994-09-24',
// ]
```

#### `toJson()`

Returns JSON representation created from the data in the bucket:

```php
$bucket->toJson();

// {
//     "username": "Jessica",
//     "profile": {
//         "nickname": "SpaceRanger45",
//         "biography": "Flying through life like astronauts fly through space.",
//         "hobbies": [
//             "Tennis",
//             "Movies",
//             "Drawing"
//         ],
//         "gender": "F",
//         "public_profile": 1
//     },
//     "progress": {
//         "essentials": 65
//     },
//     "age": 26,
//     "birthdate": "1994-09-24"
// }
    
```
