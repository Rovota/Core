---
description: 'Namespace: Rovota\Core\Support'
---

# Collections

{% hint style="danger" %}
This article is still being written. Various parts are empty or incomplete.
{% endhint %}

### Introduction

Unlike some other languages, PHP does not provide the ability to chain methods to an array, forcing you to provide the result of each function as the parameter of another.

This is rather inconvenient, since it makes code much less readable. Core solves this by providing a Collection object, which is essentially a wrapper of the standard array implementation. There are a vast variety of methods available to you, making it trivial to work with data sets.

### Compatibility

The Collection class implements `ArrayAccess`, `Iterator`, `Countable`, and `JsonSerializable`. This allows collections to be used as if they were arrays in many places, with the exception of parameters that require a value of type `array`. In these cases, you can use the [`toArray()`](collections.md#toarray) method.

### Creation

Collection objects can be created and obtained through various ways. Most commonly, you'll interact with collections through the built-in [Query Builder](databases/query-builder.md) results.

Alternatively, you can create collections yourself using the `collect()` helper function or the static `make()` method:

```php
$collection = collect([1, 2, 3, 4]);

// is equivalent to

use Rovota\Core\Support\Collection;

$collection = Collection::make([1, 2, 3, 4]);
```

### Extending

Collections support Macro functionality. This allows you to add any method you need if it isn't available by default:

```php
use Rovota\Core\Support\Collection;

Collection::macro('stringLength', function () {
    return $this->map(function ($value) {
        return strlen($value);
    });
});

collect(['car', 'bicycle', 'train'])->stringLength();

// [3, 7, 5]
```

### Methods

Nearly all of the following methods may be chained to easily work with the underlying array of a collection object. Please note that most methods return a new instance, rather than modifying the existing one, allowing you to keep the original when needed.

| [add](collections.md#undefined)             | [has](collections.md#has)                         | [pull](collections.md#pull)                         | [toJson](collections.md#tojson)             |
| ------------------------------------------- | ------------------------------------------------- | --------------------------------------------------- | ------------------------------------------- |
| [all](collections.md#all)                   | [hasAll](collections.md#hasall)                   | [push](collections.md#push)                         | [toQuery](collections.md#toquery)           |
| [append](collections.md#append)             | [hasAny](collections.md#hasany)                   | [put](collections.md#put)                           | [transform](collections.md#transform)       |
| [avg](collections.md#avg)                   | [hasNone](collections.md#hasnone)                 | [random](collections.md#random)                     | [unless](collections.md#unless)             |
| [chunk](collections.md#chunk)               | [implode](collections.md#implode)                 | [range](collections.md#range)                       | [values](collections.md#values)             |
| [collapse](collections.md#collapse)         | [intersect](collections.md#intersect)             | [reduce](collections.md#reduce)                     | [when](collections.md#when)                 |
| [collect](collections.md#collect)           | [intersectByKeys](collections.md#intersectbykeys) | [reject](collections.md#reject)                     | [whenEmpty](collections.md#whenempty)       |
| [combine](collections.md#combine)           | [isEmpty](collections.md#isempty)                 | [replace](collections.md#replace)                   | [whenNotEmpty](collections.md#whennotempty) |
| [concat](collections.md#concat)             | [isList](collections.md#islist)                   | [replaceRecursive](collections.md#replacerecursive) | [whereNotNull](collections.md#wherenotnull) |
| [contains](collections.md#contains)         | [isNotEmpty](collections.md#isnotempty)           | [resetKeys](collections.md#resetkeys)               | [whereNull](collections.md#wherenull)       |
| [containsAll](collections.md#containsall)   | [join](collections.md#join)                       | [reverse](collections.md#reverse)                   |                                             |
| [containsAny](collections.md#containsany)   | [keyBy](collections.md#keyby)                     | [search](collections.md#search)                     |                                             |
| [containsNone](collections.md#containsnone) | [keys](collections.md#keys)                       | [shift](collections.md#shift)                       |                                             |
| [count](collections.md#count)               | [last](collections.md#last)                       | [shuffle](collections.md#shuffle)                   |                                             |
| [countBy](collections.md#countby)           | [macro](collections.md#macro)                     | [skip](collections.md#skip)                         |                                             |
| [diff](collections.md#diff)                 | [map](collections.md#map)                         | [skipUntil](collections.md#skipuntil)               |                                             |
| [diffAssoc](collections.md#diffassoc)       | [max](collections.md#max)                         | [skipWhile](collections.md#skipwhile)               |                                             |
| [diffKeys](collections.md#diffkeys)         | [median](collections.md#median)                   | [slice](collections.md#slice)                       |                                             |
| [duplicates](collections.md#duplicates)     | [merge](collections.md#merge)                     | [sort](collections.md#sort)                         |                                             |
| [each](collections.md#each)                 | [min](collections.md#min)                         | [sortBy](collections.md#sortby)                     |                                             |
| [every](collections.md#every)               | [missing](collections.md#missing)                 | [sortByDesc](collections.md#sortbydesc)             |                                             |
| [except](collections.md#except)             | [mode](collections.md#mode)                       | [sortDesc](collections.md#sortdesc)                 |                                             |
| [fields](collections.md#fields)             | [modify](collections.md#modify)                   | [sortKeys](collections.md#sortbykeys)               |                                             |
| [filter](collections.md#filter)             | [occurrences](collections.md#occurrences)         | [sortKeysDesc](collections.md#sortbykeysdesc)       |                                             |
| [first](collections.md#first)               | [only](collections.md#only)                       | [sum](collections.md#sum)                           |                                             |
| [flip](collections.md#flip)                 | [partition](collections.md#partition)             | [take](collections.md#take)                         |                                             |
| [flush](collections.md#flush)               | [pipe](collections.md#pipe)                       | [takeFrom](collections.md#takefrom)                 |                                             |
| [forget](collections.md#forget)             | [pluck](collections.md#pluck)                     | [takeUntil](collections.md#takeuntil)               |                                             |
| [get](collections.md#get)                   | [pop](collections.md#pop)                         | [takeWhile](collections.md#takewhile)               |                                             |
| [groupBy](collections.md#groupby)           | [prepend](collections.md#prepend)                 | [toArray](collections.md#toarray)                   |                                             |

### Examples

#### `add()`

Adds a new value at the end of the collection, using a numeric key:

```php
collect(['apple', 'banana'])->add('blueberries');

// ['apple', 'banana', 'blueberries']
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `all()`

Returns the array used internally by the collection:

```php
collect(['apple', 'banana', 'blueberries'])->all();

// ['apple', 'banana', 'blueberries']
```

{% hint style="info" %}
This method is an alias of [`toArray()`](collections.md#toarray).
{% endhint %}

#### `append()`

Adds the value or values to the end of the collection. Optionally, you can provide a custom key as second parameter:

```php
collect(['username' => 'mike'])->append(34, 'age');

// ['username' => 'mike', 'age' => 34]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `avg()`

Retrieve the average value of a given key or the average of the collection:

```php
collect([1, 2, 3, 4, 5, 6])->avg();

// 3.5
```

Alternatively, you can specify a key as the first parameter, giving you the average of the specified key instead:

```php
collect([
    ['age' => 19],
    ['age' => 45],
    ['age' => 23],
])->avg('age');

// 29
```

{% hint style="info" %}
By default, no rounding is applied to the result of this method. To enable this, provide `true` as the second parameter or use "round" as named argument.
{% endhint %}

#### `chunk()`

Split the collection into chunks of a given size:

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->chunk(3);

// [[1, 2, 3], [4, 5, 6], [7, 8]]
```

#### `collapse()`

Collapse a collection of arrays into a single, flat collection:

```php
collect([
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
])->collapse();

// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

#### `collect()`

Create a new collection using the items in this collection:

#### `combine()`

Combines the values of the current collection (as keys) with the values provided:

```php
collect(['make', 'model'])->combine(['Mercedes', 'E63']);

// ['make' => 'Mercedes', 'model' => 'E63']
```

#### `concat()`

Appends the values of a given array or collection onto the end of the current collection:

```php
collect(['Richard Peterson'])->concat(['name' => 'Emily Hartman']);

// ['Richard Peterson', 'Emily Hartman']
```

#### `contains()`

Checks whether the provided value exists in the collection:

```php
collect([1, 2, 3, 4, 5, 6])->contains(8);

// false
```

Alternatively, you can use a closure to check whether an item exists matching a truth test:

```php
collect(['Mike', 'Richard', 'Linda'])->contains(function ($name) {
    return str_starts_with($name, 'M');
});

// true
```

#### `containsAll()`

Checks whether all given values are present:

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAll(['Apple', 'Mango']);

// false
```

#### `containsAny()`

Checks whether at least one of the given values is present:

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAny(['Apple', 'Mango']);

// true
```

#### `containsNone()`

Checks whether none of the given values are present:

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAll(['Pineapple', 'Orange]);

// true
```

#### `count()`

Returns the total number of items in the collection, or the total number of items for the given key:

```php
collect(1, 4, 9, 8, 3, 7, 2)->count();

// 7
```

```php
collect([
    'name' => 'Thomas',
    'grades' => [4, 8, 2, 9, 7]
])->count('grades');

// 5
```

#### `countBy()`

Counts all occurrences of each value in the collection:

```php
collect([1, 2, 2, 2, 3])->countBy();

// [1 => 1, 2 => 3, 3 => 1]
```

Alternatively, you could pass a callback to count all values using custom logic:

{% code overflow="wrap" %}

```php
use Rovota\Core\Support\Str;

$addresses = [
    'cherles@hotmail.com',
    'william@gmail.com',
    'david@hotmail.com'
];

collect($addresses)->countBy(function ($email) {
    return Str::after($email, '@');
});

// ['hotmail.com' => 2, 'gmail.com' => 1]
```
{% endcode %}

#### `diff()`

Returns the values in the current collection that are not present in the given collection:

```php
collect([1, 2, 3, 4, 5])->diff([2, 4, 6, 8]);

// [1, 3, 5]
```

#### `diffAssoc()`

Returns the key and value pairs in the current collection that are not present in the given collection:

```php
collect(['age' => 52, 'employee' => true])->diffAssoc(['age' => 34, 'employee' => true]);

// ['age' => 52]
```

#### `diffKeys()`

Returns the keys with their values in the current collection that are not present in the given collection:

```php
collect(['age' => 52, 'employee' => true])->diffAssoc(['employee' => true]);

// ['age' => 52]
```

#### `duplicates()`

Returns all duplicate values in the collection with their original keys:

```php
collect(['Ford', 'Mercedes', 'Mazda', 'Ford', 'Renault', 'Mazda']);

// [3 => 'Ford', 5 => 'Mazda']
```

Alternatively, a key can be provided:

```php
collect([
    ['brand' => 'Google'],
    ['brand' => 'Reddit'],
    ['brand' => 'Microsoft'],
    ['brand' => 'Google'],
])->duplicates('brand');

// [3 => 'Google']
```

Or, you could provide a callback in which you customize the values used:

```php
use Rovota\Core\Support\Str;

collect([
    'alicia.daniels@gmail.com',
    'mark.robinson@gmail.com',
    'alicia.keys@gmail.com',
    'peter.s.williams@gmail.com',
])->duplicates(function ($email) {
    return Str::before($email, '@');
});

// [2 => 'alicia.daniels']
```

{% hint style="info" %}
This method compares all values using strict comparisons.
{% endhint %}

#### `each()`

Iterates over all items in the collection and passes the item to the given callback. Stops iterating when `false` is returned, or the end of the collection is reached:

```php
$collection->each(function ($value, $key) {
    if ($key === 30) {
        return false; // stops iterating
    }
    // do something
});
```

#### `every()`

Returns `true` when all items pass a given truth test using the given callback:

```php
collect([45, 87, 23, 79, 48])->every(function ($number) {
    return $number > 5;
});

// true
```

{% hint style="info" %}
This method will always return `true` when the collection is empty.
{% endhint %}

#### `except()`

Creates a new collection using all items of the current collection except for those with the given key(s):

```php
collect(['country' => 'NL', 'city' => 'Amsterdam'])->except(['country']);

// ['city' => 'Amsterdam']
```

#### `fields()`

Creates a new collection with each item only keeping the fields specified:

```php
collect([
    ['name' => 'Mike', 'age' => 35],
    ['name' => 'Anthony', 'age' => 28],
    ['name' => 'Steven', 'age' => 47],
])->fields(['name']);

// [
//    ['name' => 'Mike'],
//    ['name' => 'Anthony'],
//    ['name' => 'Steven'],
// ]
```

#### `filter()`

Returns the items from the collection that pass a given truth test. When no callback is provided, items with a value of `null` will be removed:

```php
collect([1, 'Porsche', null, 67, 'Pineapple'])->filter();

// [1, 'Porsche', 67, 'Pineapple']
```

```php
collect([1, 'Porsche', null, 67, 'Pineapple'])->filter(function ($value) {
    return is_string($value);
});

// ['Porsche', 'Pineapple']
```

#### `first()`

Returns the first item in the collection, optionally the first that passes a given truth test:

```php
collect(['AMD', 'Intel', 'Nvidia'])->first();

// AMD
```

```php
collect(['AMD', 'Intel', 'Nvidia'])->first(function ($brand) {
    return strlen($brand) > 4;
});

// Intel
```

#### `flip()`

Swaps the keys with their corresponding values:

```php
collect(['make' => 'Audi', 'model' => 'RS7'])->flip();

// ['Audi' => 'make', 'RS7' => 'model']
```

#### `flush()`

Empties the collection completely:

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->flush();

// []
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `forget()`

Removes one or multiple items by their key(s):

```php
collect(['name' => 'Mike', 'age' => 47])->forget('name');

// ['age' => 47]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `get()`

Returns the value for a given key. If the key does not exist, the default value is returned:

```php
collect(['name' => 'Mike', 'age' => 47])->get('age');
// 47
collect(['name' => 'Mike', 'age' => 47])->get('score', 10);
// 10
```

#### `groupBy()`

Return a new collection grouped by the given key, or a value returned from a callback:

```php
collect([
    ['name' => 'Mike', 'age' => 37],
    ['name' => 'Mike', 'age' => 62],
    ['name' => 'Anthony', 'age' => 46],
])->groupBy('name');

// [
//     'Mike' => [
//         ['name' => 'Mike', 'age' => 37],
//         ['name' => 'Mike', 'age' => 62],
//     ],
//     'Anthony' => [
//         ['name' => 'Anthony', 'age' => 46],
//     ]
// ]
```

#### `has()`

Checks whether a key is present:

```php
collect(['name' => 'Mike', 'age' => 45])->has('email');

// false
```

#### `hasAll()`

Checks whether all given keys are present:

```php
collect(['name' => 'Mike', 'age' => 45])->hasAll(['email', 'age']);

// false
```

#### `hasAny()`

Checks whether at least one of the given keys is present:

```php
collect(['name' => 'Mike', 'age' => 45])->hasAny(['email', 'age']);

// true
```

#### `hasNone()`

Checks whether none of the given keys are present:

```php
collect(['name' => 'Mike', 'age' => 45])->hasNone(['email', 'phone']);

// true
```

#### `implode()`

Joins items in a collection together using "glue". When the collection contains arrays or objects, you need to provide the key you want to join:

```php
collect(['red', 'green', 'orange'])->implode('-');

// red-green-orange
```

```php
collect([
    ['name' => 'Mike', 'age' => 35],
    ['name' => 'Joshua', 'age' => 35],
    ['name' => 'Edward', 'age' => 35],
])->implode('age', '-');

// 35-28-41
```

#### `intersect()`

Removes all values from the collection that are not present in the given collection:

```php
// Example coming soon
```

#### `intersectByKeys()`

Removes all keys from the collection that are not present in the given collection:

```php
// Example coming soon
```

#### `isEmpty()`

Returns `true` when no items are present in the collection:

```php
collect(['red', 'green'])->isEmpty();

// false
```

#### `isList()`

Returns `true` when the collection keys are numeric, in ascending order, starting by 0:

```php
collect([1, 4, 5, 6, 7, 9])->isList();

// false
```

#### `isNotEmpty()`

Returns `true` when at least one item is present in the collection:

```php
collect(['red', 'green'])->isNotEmpty();

// true
```

#### `join()`

Joins the values of the collection together. The second argument can be used to specify how the final element should be appended:

```php
collect(['Google', 'Samsung', 'Apple'])->join(', ');
// Google, Samsung, Apple
collect(['Google', 'Samsung', 'Apple'])->join(', ', ' and ');
// Google, Samsung and Apple
collect(['Google', 'Samsung'])->join(', ', ' and ');
// Google and Samsung
```

#### `keyBy()`

Keys the collection using the given key, or the result of the callback:

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
    ['name' => 'Sharon', 'age' => 41],
])->keyBy('name');

// [
//     'Kevin' => ['name' => 'Kevin', 'age' => 35],
//     'Emily' => ['name' => 'Emily', 'age' => 26],
//     'Sharon' => ['name' => 'Sharon', 'age' => 41],
// ]
```

#### `keys()`

Returns the keys present in the collection:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->keys();

// ['color', 'width', 'height']
```

#### `last()`

Returns the last item in a collection, optionally the last the passes a given truth test:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->last();

// 250
```

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->last(function ($value) {
    return is_int($value) && $value < 200;
});

// 100
```

#### `macro()`

See the [Extending](collections.md#extending) section for more information.

#### `map()`

Iterates through the collection allowing modification of each item, returning a new collection with the result:

```php
collect(['911', 'Taycan', 'Panamera'])->map(function ($model) {
    return 'Porsche '.$model;
});

// ['Porsche 911', 'Porsche Taycan', 'Porsche Panamera']
```

#### `max()`

Returns the highest value in a collection or for a given key, limited by the value of the second parameter:

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->max();
// 8
collect([1, 2, 3, 4, 5, 6, 7, 8])->max(limit: 5);
// 5
```

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
    ['name' => 'Sharon', 'age' => 41],
])->max('age');

// 41
```

#### `median()`

Returns the median of the collection or for a given key:

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->median();
// 4.5
```

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
    ['name' => 'Sharon', 'age' => 41],
])->median('age');

// 35
```

#### `merge()`

Merges the current collection with the items in the new collection:

```php
// Example coming soon
```

#### `min()`

Returns the lowest value in a collection or for a given key, limited by the value of the second parameter:

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->min();
// 1
collect([1, 2, 3, 4, 5, 6, 7, 8])->min(limit: 5);
// 5
```

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
    ['name' => 'Sharon', 'age' => 41],
])->min('age');

// 26
```

#### `missing()`

Checks whether the key or keys are missing:

```php
collect(['color' => 'red', 'label' => 'Sale'])->missing('color');

// false
```

#### `mode()`

Returns the mode of the collection or given key:

```php
collect([1, 2, 3, 3, 4, 5, 6])->mode();
// [3]
collect([1, 3, 3, 4, 5, 5])->mode();
// [3, 5]
```

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 35],
    ['name' => 'Sharon', 'age' => 41],
])->mode('age');

// [35]
```

#### `modify()`

Allows modifying the collection using a closure:

```php
collect([1, 2, 3, 4, 5, 6])->transform(function ($number) {
    return $number * 2;
});

// [2, 4, 6, 8, 10, 12]
```

{% hint style="info" %}
This method returns a new Collection instance. If you want to modify the original collection, use [`transform()`](collections.md#transform) instead.
{% endhint %}

#### `occurrences()`

Returns how many times a given value exists in the collection:

```php
collect([1, 2, 3, 3, 4, 6, 7, 8])->occurrences(3);

// 2
```

#### `only()`

Returns the item with the specified key, or all items with the specified key(s):

```php
collect(['country' => 'NL', 'city' => 'Amsterdam'])->only(['country']);

// ['country' => 'NL']
```

#### `partition()`

Splits the collection into two collections, where one has items passing the truth test, and the other has items failing the truth test:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->partition(function ($value) {
    return is_int($value);
});

// [
//     0 => [ 
//         'width' => 100,
//         'height' => 250,
//     ],
//     1 => [
//         'color' => 'red',
//     ],
// ]
```

#### `pipe()`

Passes the collection to the callback and returns the result:

```php
// Example coming soon
```

#### `pluck()`

Retrieves all values for a given key:

```php
$collection = collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
])->pluck('age');

// [35, 26]
```

Optionally, you can specify how the items should be keyed using the second parameter:

```php
$collection = collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
])->pluck('age', 'name');

// ['Kevin' => 35, 'Emily' => 26]
```

#### `pop()`

Removes and returns the last item or `x` items from the collection. Defaults to `1`:

```php
collect([1, 2, 3, 4, 5])->pop();
// 5
collect([1, 2, 3, 4, 5])->pop(3);
// [5, 4, 3]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `prepend()`

Adds the value or values to the beginning of the collection:

```php
collect(['username' => 'mike'])->prepend(34, 'age');

// ['age' => 34, 'username' => 'mike']
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `pull()`

Retrieves the item for the given key and removes it from the collection:

```php
collect(['username' => 'mike', 'age' => 34])->pull('age');
// 34
collect(['username' => 'mike', 'age' => 34])->pull('score', 10);
// 10
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `push()`

Adds a new value to the end of the collection:

```php
collect(['username' => 'mike', 'age' => 34])->push('score');

// ['username' => 'mike', 'age' => 34, 'score']
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `put()`

Adds a new value for the given key:

```php
collect(['username' => 'mike', 'age' => 34])->put('score', 10);

// ['username' => 'mike', 'age' => 34, 'score' => 10]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `random()`

Returns `x` random items from the collection. Defaults to `1`:

```php
collect([1, 2, 3, 4, 5])->random();
// 4
collect([1, 2, 3, 4, 5])->random(3);
// [1, 4, 5]
```

#### `range()`

Returns the result of the maximum value minus the minimum value:

```php
collect([1, 2, 3, 3, 4, 5, 6])->range();
// 5
```

```php
collect([
    ['name' => 'Kevin', 'age' => 35],
    ['name' => 'Emily', 'age' => 26],
    ['name' => 'Sharon', 'age' => 41],
])->range('age');

// [15]
```

#### `reduce()`

Reduces the collection to a single value, passing the result of each iteration into the next:

```php
// Example coming soon
```

#### `reject()`

Returns all items from the collection except those that pass the truth test:

```php
collect([1, 2, 3, 3, 4, 5, 6])->reject(function ($number) {
    return is_odd($number);
})

// [2, 4, 6]
```

#### `replace()`

Replaces the items in the current collection with the items in the given collection:

```php
// Example coming soon
```

#### `replaceRecursive()`

Similar to `replace()`, but applies the same process to inner values:

```php
// Example coming soon
```

#### `resetKeys()`

Remove existing keys and replace them with consecutive keys starting from 0:

```php
collect(['username' => 'mike', 'age' => 34])->resetKeys();

// [0 => 'mike', 1 => 34]
```

#### `reverse()`

Reverses the order of the items in the collection while preserving the keys:

```php
collect(['username' => 'mike', 'age' => 34])->reverse();

// ['age' => 34, 'username' => 'mike']
```

#### `search()`

Returns the corresponding key of the searched value when found. Uses strict comparisons by default:

```php
collect(['Alpine', 'Mercedes', 'BMW', 'Volkswagen'])->search('BMW');

// 2
```

Optionally, you can pass a closure to search for the first item that matches a truth test:

```php
collect(['Alpine', 'Mercedes', 'BMW', 'Volkswagen'])->search(function ($brand) {
    return Text::startsWith($brand, 'M');
});

// 1
```

#### `shift()`

Removes and returns the first item or `x` items from the collection. Defaults to `1`:

```php
collect([1, 2, 3, 4, 5])->shift();
// 1
collect([1, 2, 3, 4, 5])->shift(3);
// [1, 2, 3]
```

#### `shuffle()`

Changes the order of the items in the collection to be random:

```php
collect([1, 2, 3, 4, 5, 6])->shuffle();

// [2, 5, 3, 1, 6, 4]
```

#### `skip()`

Skips over `x` items from the collection, and returns the remaining items:

```php
collect([1, 2, 3, 4, 5, 6])->skip(2);

// [3, 4, 5, 6]
```

#### `skipUntil()`

Skips over the items from the collection until the closure returns `true`, and returns the remaining items:

```php
collect([1, 2, 3, 4, 5, 6])->skipUntil(function ($number) {
    return $number > 3;
});

// [4, 5, 6]
```

#### `skipWhile()`

Skips over the items from the collection as long as the closure returns `true`, and returns the remaining items:

```php
collect([1, 2, 3, 4, 5, 6])->skipWhile(function ($number) {
    return $number < 3;
});

// [3, 4, 5, 6]
```

#### `slice()`

Returns a slice of the collection starting at the given index, with a maximum number of items if defined:

```php
collect([1, 2, 3, 4, 5, 6])->slice(3, 2);

// [4, 5]
```

#### `sort()`

Sorts the array by value, while preserving the array keys. For custom behavior, pass your own algorithm to the `callback` parameter:

```php
collect([2, 5, 3, 1, 6, 4])->sort();

// [1, 2, 3, 4, 5, 6]
```

#### `sortBy()`

Sort the items using the result of the provided closure:

```php
// Example coming soon
```

#### `sortByDesc()`

Sort the items in descending order using the result of the provided closure:

```php
// Example coming soon
```

#### `sortDesc()`

Sorts the array by value in descending order, while preserving the array keys:

```php
collect([2, 5, 3, 1, 6, 4])->sortDesc();

// [6, 5, 4, 3, 2, 1]
```

#### `sortKeys()`

Sorts the array by key:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->sortKeys();

// ['color' => 'red', 'height' => 250, 'width' => 100]
```

#### `sortKeysDesc()`

Sorts the array by key in descending order:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->sortKeysDesc();

// ['width' => 100, 'height' => 250, 'color' => 'red']
```

#### `sum()`

Returns the sum of all items in the collection, the specified key or using a closure:

```php
collect([1, 2, 3, 3, 4, 5, 6])->sum();
// 21
```

```php
collect([
    ['name' => 'Chair', 'stock' => 27],
    ['name' => 'Desk', 'stock' => 41],
    ['name' => 'Lamp', 'stock' => 182],
])->sum('stock');

// [250]
```

#### `take()`

Returns `x` number of items from the original collection:

```php
collect([1, 2, 3, 4, 5, 6])->take(2);

// [1, 2]
```

#### `takeFrom()`

Returns all items after the specified value or when an item passes the truth test:

```php
collect([1, 2, 3, 4, 5, 6])->takeFrom(2);

// [3, 4, 5, 6]
```

```php
collect([1, 2, 3, 4, 5, 6])->takeFrom(function ($number) {
    return $number > 3; // from 4
});

// [5, 6]
```

#### `takeUntil()`

Returns all items up until the value has been found or an item passes a truth test:

```php
collect([1, 2, 3, 4, 5, 6])->takeUntil(5);

// [1, 2, 3, 4]
```

```php
collect([1, 2, 3, 4, 5, 6])->takeUntil(function ($number) {
    return $number > 3; // until 4
});

// [1, 2, 3]
```

#### `takeWhile()`

Returns all items until an item fails the given truth test:

```php
collect([1, 2, 3, 4, 5, 6])->takeWhile(function ($number) {
    return $number < 3;
});

// [1, 2]
```

#### `toArray()`

Returns array representation created from the data in the collection:

```php
collect([1, 2, 3, 4, 5, 6])->toArray();

// [1, 2, 3, 4, 5, 6]
```

#### `toJson()`

Returns JSON representation created from the data in the collection:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->toJson();

// {"color":"red","width":100,"height":250}
```

#### `toQuery()`

Returns a formatted query string using the items in the collection:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->toQuery();

// ?color=red&width=100&height=250
```

#### `transform()`

Iterates over all items in the collection and replaces all values with the values returned by the callback:

```php
collect([1, 2, 3, 4, 5, 6])->transform(function ($number) {
    return $number * 2;
});

// [2, 4, 6, 8, 10, 12]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `unless()`

Executes the provided callback when the condition is `false`. Optionally, when `true`, the alternative callback will be executed:

```php
// Example coming soon
```

#### `values()`

Returns the values of the collection without the original keys:

```php
collect(['color' => 'red', 'width' => 100, 'height' => 250])->values();

// ['red', 100, 250]
```

#### `when()`

Executes the provided callback when the condition is `true`. Optionally, when `false`, the alternative callback will be executed:

```php
// Example coming soon
```

#### `whenEmpty()`

Executes the callback when no items are present. Optionally, when not empty, the alternative callback will be executed:

```php
// Example coming soon
```

#### `whenNotEmpty()`

Executes the callback when at least one item is present. Optionally, when empty, the alternative callback will be executed:

```php
// Example coming soon
```

#### `whereNotNull()`

Returns all items where the value is not equivalent to `null`:

```php
collect(['name' => 'Mike' => 'age' => 35, 'alias' => null])->whereNotNull();

// ['name' => 'Mike' => 'age' => 35]
```

#### `whereNull()`

Returns all items where the value is the equivalent to `null`:

```php
collect(['name' => 'Mike' => 'age' => 35, 'alias' => null])->whereNull();

// ['alias' => null]
```