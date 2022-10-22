---
description: 'Namespace: Rovota\Core\Support'
---

# Collections

### Introduction

Unlike some other languages, PHP does not provide the ability to chain methods to an array, forcing you to provide the result of each function as the parameter of another.

This is rather inconvenient, since it makes code much less readable. Core solves this by providing a Collection object, which is essentially a wrapper of the standard array implementation. There are a vast variety of methods available to you, making it trivial to work with data sets.

### Compatibility

The Collection class implements `ArrayAccess`, `Iterator`, `Countable`, and `JsonSerializable`. This allows collections to be used as if they were arrays in many places, with the exception of parameters that require a value of type `array`. In this case, you can use the `toArray()` method.

### Creation

Collection objects can be created and obtained through various ways. Most commonly, you'll interact with collections through the built-in [Query Builder](databases/query-builder.md).

Alternatively, you can create collections yourself using the `collect()` helper function or the static `make()` method:

```php
$collection = collect([1, 2, 3, 4]);
// Is equivalent to
$collection = Collection::make([1, 2, 3, 4]);
```

### Extending

Collections support Macro functionality. This allows you to add any method you need if it isn't available to you as standard:

```php
use Rovota\Core\Support\Collection;

Collection::macro('stringLength', function() {
    return $this->map(function ($value) {
        return str_length($value);
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
| countBy                                     | macro           | shuffle          |              |
| diff                                        | map             | skip             |              |
| diffAssoc                                   | max             | skipUntil        |              |
| diffKeys                                    | median          | skipWhile        |              |
| duplicates                                  | merge           | slice            |              |
| each                                        | min             | sort             |              |
| every                                       | missing         | sortBy           |              |
| except                                      | mode            | sortByDesc       |              |
| fields                                      | modify          | sortDesc         |              |
| filter                                      | occurrences     | sortKeys         |              |
| first                                       | only            | sortKeysDesc     |              |
| flip                                        | partition       | sum              |              |
| flush                                       | pipe            | take             |              |
| forget                                      | pluck           | takeFrom         |              |
| get                                         | pop             | takeUntil        |              |
| groupBy                                     | prepend         | takeWhile        |              |

### Examples

#### `add()`

Adds a new value at the end of the collection, using a numeric key.

```php
collect(['apple', 'banana'])->add('blueberries');

// ['apple', 'banana', 'blueberries']
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `all()`

Returns the array used internally by the collection.

```php
collect(['apple', 'banana', 'blueberries'])->all();

// ['apple', 'banana', 'blueberries']
```

#### `append()`

Adds the value or values to the end of the collection. Optionally, you can provide a custom key as second parameter.

```php
collect(['username' => 'mike'])->append(34, 'age');

// ['username' => 'mike', 'age' => 34]
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `avg()`

Retrieve the average value of a given key or the average of the collection.

```php
collect([1, 2, 3, 4, 5, 6])->avg();

// 3.5
```

Alternatively, you can specify a key as the first parameter, giving you the average of the specified key instead.

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

Split the collection into chunks of a given size.

```php
collect([1, 2, 3, 4, 5, 6, 7, 8])->chunk(3);

// [[1, 2, 3], [4, 5, 6], [7, 8]]
```

#### `collapse()`

Collapse a collection of arrays into a single, flat collection.

```php
collect([
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
])->collapse();

// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

#### `collect()`

Create a new collection using the items in this collection.

#### `combine()`

Combines the values of the current collection (as keys) with the values provided.

```php
collect(['make', 'model'])->combine(['Mercedes', 'E63']);

// ['make' => 'Mercedes', 'model' => 'E63']
```

#### `concat()`

Appends the values of a given array or collection onto the end of the current collection.

```php
collect(['Richard Peterson'])->concat(['name' => 'Emily Hartman']);

// ['Richard Peterson', 'Emily Hartman']
```

#### `contains()`

Checks whether the provided value exists in the collection.

```php
collect([1, 2, 3, 4, 5, 6])->contains(8);

// false
```

Alternatively, you can use a callback to check whether an item exists matching a truth test.

```php
collect(['Mike', 'Richard', 'Linda'])->contains(function($name) {
    return str_starts_with($name, 'M');
});

// true
```

#### `containsAll()`

Checks whether all given values are present.

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAll(['Apple', 'Mango']);

// false
```

#### `containsAny()`

Checks whether at least one of the given values is present.

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAny(['Apple', 'Mango']);

// true
```

#### `containsNone()`

Checks whether none of the given values are present.

```php
collect(['Banana', 'Avocado', 'Apple'])->containsAll(['Pineapple', 'Orange]);

// true
```

#### `count()`

Returns the total number of items in the collection, or the total number of items for the given key.

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

#### `diff()`

#### `diffAssoc()`

#### `diffKeys()`

#### `duplicates()`

#### `each()`

#### `every()`

#### `except()`

#### `fields()`

#### `filter()`

#### `first()`

#### `flip()`

#### `flush()`

#### `forget()`

#### `get()`

#### `groupBy()`

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
