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

The Collection class implements `ArrayAccess`, `Iterator`, `Countable`, and `JsonSerializable`. This allows collections to be used as if they were arrays in many places, with the exception of parameters that require a value of type `array`. In these cases, you can use the `toArray()` method.

### Creation

Collection objects can be created and obtained through various ways. Most commonly, you'll interact with collections through the built-in [Query Builder](databases/query-builder.md) results.

Alternatively, you can create collections yourself using the `collect()` helper function or the static `make()` method:

```php
$collection = collect([1, 2, 3, 4]);
// is equivalent to
$collection = Collection::make([1, 2, 3, 4]);
```

### Extending

Collections support Macro functionality. This allows you to add any method you need if it isn't available by default:

```php
use Rovota\Core\Support\Collection;

Collection::macro('stringLength', function() {
    return $this->map(function ($value) {
        return strlen($value);
    });
});

collect(['car', 'bicycle', 'train'])->stringLength();

// [3, 7, 5]
```

### Methods

Nearly all of the following methods may be chained to easily work with the underlying array of a collection object. Please note that most methods return a new instance, rather than modifying the existing one, allowing you to keep the original when needed.

| [add](collections.md#undefined)             | has             | pull             | toArray      |
| ------------------------------------------- | --------------- | ---------------- | ------------ |
| [all](collections.md#all)                   | hasAll          | push             | toJson       |
| [append](collections.md#append)             | hasAny          | put              | toQuery      |
| [avg](collections.md#avg)                   | hasNone         | random           | transform    |
| [chunk](collections.md#chunk)               | implode         | range            | unless       |
| [collapse](collections.md#collapse)         | intersect       | reduce           | values       |
| [collect](collections.md#collect)           | intersectByKeys | reject           | when         |
| [combine](collections.md#combine)           | isEmpty         | replace          | whenEmpty    |
| [concat](collections.md#concat)             | isList          | replaceRecursive | whenNotEmpty |
| [contains](collections.md#contains)         | isNotEmpty      | resetKeys        | whereNotNull |
| [containsAll](collections.md#containsall)   | join            | reverse          | whereNull    |
| [containsAny](collections.md#containsany)   | keyBy           | rewind           |              |
| [containsNone](collections.md#containsnone) | keys            | search           |              |
| [count](collections.md#count)               | last            | shift            |              |
| [countBy](collections.md#countby)           | macro           | shuffle          |              |
| [diff](collections.md#diff)                 | map             | skip             |              |
| [diffAssoc](collections.md#diffassoc)       | max             | skipUntil        |              |
| [diffKeys](collections.md#diffkeys)         | median          | skipWhile        |              |
| [duplicates](collections.md#duplicates)     | merge           | slice            |              |
| [each](collections.md#each)                 | min             | sort             |              |
| [every](collections.md#every)               | missing         | sortBy           |              |
| [except](collections.md#except)             | mode            | sortByDesc       |              |
| [fields](collections.md#fields)             | modify          | sortDesc         |              |
| [filter](collections.md#filter)             | occurrences     | sortKeys         |              |
| [first](collections.md#first)               | only            | sortKeysDesc     |              |
| [flip](collections.md#flip)                 | partition       | sum              |              |
| [flush](collections.md#flush)               | pipe            | take             |              |
| [forget](collections.md#forget)             | pluck           | takeFrom         |              |
| [get](collections.md#get)                   | pop             | takeUntil        |              |
| [groupBy](collections.md#groupby)           | prepend         | takeWhile        |              |

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
collect(['Mike', 'Richard', 'Linda'])->contains(function($name) {
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

Alternatively, you could pass a closure to count all values using custom logic:

{% code overflow="wrap" %}
```php
use Rovota\Core\Support\Text;

$addresses = [
    'cherles@hotmail.com',
    'william@gmail.com',
    'david@hotmail.com'
];

collect($addresses)->countBy(function ($email) {
    return Text::after($email, '@');
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

Or, you could provide a closure in which you customize the values used:

```php
use Rovota\Core\Support\Text;

collect([
    'alicia.daniels@gmail.com',
    'mark.robinson@gmail.com',
    'alicia.keys@gmail.com',
    'peter.s.williams@gmail.com',
])->duplicates(function ($email) {
    return Text::before($email, '@');
});

// [2 => 'alicia.daniels']
```

{% hint style="info" %}
This method compares all values using strict comparisons.
{% endhint %}

#### `each()`

Iterates over all items in the collection and passes the item to the given closure. Stops iterating when `false` is returned, or the end of the collection is reached:

```php
$collection->each(function ($value, $key) {
    if ($key === 30) {
        return false; // stops iterating
    }
    // do something
});
```

#### `every()`

Returns `true` when all items pass a given truth test using the given closure:

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

Returns the items from the collection that pass a given truth test. When no closure is provided, items with a value of `null` will be removed:

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
```

#### `groupBy()`

Return a new collection grouped by the given key, or a value returned from a closure:

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

#### `hasAll()`

#### `hasAny()`

#### `hasNone()`

#### `implode()`

#### `intersect()`

#### `intersectByKeys()`

#### `isEmpty()`

#### `isList()`

#### `isNotEmpty()`

#### `join()`

#### `keyBy()`

#### `keys()`

#### `last()`

#### `macro()`

#### `map()`

#### `max()`

#### `median()`

#### `merge()`

#### `min()`

#### `missing()`

#### `mode()`

#### `modify()`

#### `occurrences()`

#### `only()`

Returns the item with the specified key, or all items with the specified key(s):

```php
collect(['country' => 'NL', 'city' => 'Amsterdam'])->only(['country']);

// ['country' => 'NL']
```

#### `partition()`

#### `pipe()`

#### `pluck()`

#### `pop()`

#### `prepend()`

#### `pull()`

#### `push()`

#### `put()`

#### `random()`

#### `range()`

#### `reduce()`

#### `reject()`

#### `replace()`

#### `replaceRecursive()`

#### `resetKeys()`

#### `reverse()`

#### `rewind()`

#### `search()`

#### `shift()`

#### `shuffle()`

#### `skip()`

#### `skipUntil()`

#### `skipWhile()`

#### `slice()`

#### `sort()`

#### `sortBy()`

#### `sortByDesc()`

#### `sortDesc()`

#### `sortByKeys()`

#### `sortByKeysDesc()`

#### `sum()`

#### `take()`

#### `takeFrom()`

#### `takeUntil()`

#### `takeWhile()`

#### `toArray()`

#### `toJson()`

#### `toQuery()`

#### `transform()`

#### `unless()`

#### `values()`

#### `when()`

#### `whenEmpty()`

#### `whenNotEmpty()`

#### `whereNotNull()`

#### `whereNull()`
