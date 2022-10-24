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
// Some code
```

#### `intersectByKeys()`

Removes all keys from the collection that are not present in the given collection:

```php
// Some code
```

#### `isEmpty()`

Returns `true` when no items are present in the collection:

```php
// Some code
```

#### `isList()`

Returns `true` when the collection keys are numeric, in ascending order, starting by 0:

```php
// Some code
```

#### `isNotEmpty()`

Returns `true` when at least one item is present in the collection:

```php
// Some code
```

#### `join()`

Joins the values of the collection together. The second argument can be used to specify how the final element should be appended:

```php
// Some code
```

#### `keyBy()`

Keys the collection using the given key, or the result of the closure:

```php
// Some code
```

#### `keys()`

Returns the keys present in the collection:

```php
// Some code
```

#### `last()`

Returns the last item in a collection, optionally the last the passes a given truth test:

```php
// Some code
```

#### `macro()`

See the [Extending](collections.md#extending) section for more information.

#### `map()`

Iterates through the collection allowing modification of each item, returning a new collection with the result:

```php
// Some code
```

#### `max()`

Returns the highest value in a collection or for a given key, limited by the value of the second parameter:

```php
// Some code
```

#### `median()`

Returns the median of the collection or for a given key:

```php
// Some code
```

#### `merge()`

Merges the current collection with the items in the new collection:

```php
// Some code
```

#### `min()`

Returns the lowest value in a collection or for a given key, limited by the value of the second parameter:

```php
// Some code
```

#### `missing()`

Checks whether the key or keys are missing:

```php
// Some code
```

#### `mode()`

Returns the mode of the collection or given key:

```php
// Some code
```

#### `modify()`

Allows modifying the collection using a closure:

```php
// Some code
```

#### `occurrences()`

Returns how many times a given value exists in the collection:

```php
// Some code
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
// Some code
```

#### `pipe()`

Passes the collection to the closure and returns the result:

```php
// Some code
```

#### `pluck()`

Retrieves all values for a given key:

```php
// Some code
```

#### `pop()`

Removes and returns the last item or `x` items from the collection. Defaults to `1`:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `prepend()`

Adds the value or values to the beginning of the collection:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `pull()`

Retrieves the item for the given key and removes it from the collection:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `push()`

Adds a new value to the end of the collection:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `put()`

Adds a new value for the given key:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `random()`

Returns `x` random items from the collection. Defaults to `1`:

```php
// Some code
```

#### `range()`

Returns the result of the maximum value minus the minimum value:

```php
// Some code
```

#### `reduce()`

Reduces the collection to a single value, passing the result of each iteration into the next:

```php
// Some code
```

#### `reject()`

Returns all items from the collection except those that pass the truth test:

```php
// Some code
```

#### `replace()`

Replaces the items in the current collection with the items in the given collection:

```php
// Some code
```

#### `replaceRecursive()`

Similar to `replace()`, but applies the same process to inner values:

```php
// Some code
```

#### `resetKeys()`

Remove existing keys and replace them with consecutive keys starting from 0:

```php
// Some code
```

#### `reverse()`

Reverses the order of the items in the collection while preserving the keys:

```php
// Some code
```

#### `search()`

Returns the corresponding key of the searched value when found. Uses strict comparisons by default:

```php
// Some code
```

Optionally, you can pass a closure to search for the first item that matches a truth test:

```php
// Some code
```

#### `shift()`

Removes and returns the first item or `x` items from the collection. Defaults to `1`:

```php
// Some code
```

#### `shuffle()`

Changes the order of the items in the collection to be random:

```php
// Some code
```

#### `skip()`

Skips over `x` items from the collection, and returns the remaining items:

```php
// Some code
```

#### `skipUntil()`

Skips over the items from the collection until the closure returns `true`, and returns the remaining items:

```php
// Some code
```

#### `skipWhile()`

Skips over the items from the collection as long as the closure returns `true`, and returns the remaining items:

```php
// Some code
```

#### `slice()`

Returns a slice of the collection starting at the given index, with a maximum number of items if defined:

```php
// Some code
```

#### `sort()`

Sorts the array by value, while preserving the array keys. For custom behavior, pass your own algorithm to the `closure` parameter:

```php
// Some code
```

#### `sortBy()`

Sort the items using the result of the provided closure:

```php
// Some code
```

#### `sortByDesc()`

Sort the items in descending order using the result of the provided closure:

```php
// Some code
```

#### `sortDesc()`

Sorts the array by value in descending order, while preserving the array keys:

```php
// Some code
```

#### `sortKeys()`

Sorts the array by key:

```php
// Some code
```

#### `sortKeysDesc()`

Sorts the array by key in descending order:

```php
// Some code
```

#### `sum()`

Returns the sum of all items in the collection, the specified key or using a closure:

```php
// Some code
```

#### `take()`

Returns `x` number of items from the original collection:

```php
// Some code
```

#### `takeFrom()`

Returns all items after the specified value or when an item passes the truth test:

```php
// Some code
```

#### `takeUntil()`

Returns all items up until the value has been found or an item passes a truth test:

```php
// Some code
```

#### `takeWhile()`

Returns all items until an item fails the given truth test:

```php
// Some code
```

#### `toArray()`

Returns array representation created from the data in the collection:

```php
// Some code
```

#### `toJson()`

Returns JSON representation created from the data in the collection:

```php
// Some code
```

#### `toQuery()`

Returns a formatted query string using the items in the collection:

```php
// Some code
```

#### `transform()`

Iterates over all items in the collection and replaces all values with the values returned by the closure:

```php
// Some code
```

{% hint style="warning" %}
This method modifies the original collection rather than creating a new instance.
{% endhint %}

#### `unless()`

Executes the provided callback when the condition is `false`. Optionally, when `true`, the alternative callback will be executed:

```php
// Some code
```

#### `values()`

Returns the values of the collection without the original keys:

```php
// Some code
```

#### `when()`

Executes the provided callback when the condition is `true`. Optionally, when `false`, the alternative callback will be executed:

```php
// Some code
```

#### `whenEmpty()`

Executes the closure when no items are present. Optionally, when not empty, the alternative closure will be executed:

```php
// Some code
```

#### `whenNotEmpty()`

Executes the closure when at least one item is present. Optionally, when empty, the alternative closure will be executed:

```php
// Some code
```

#### `whereNotNull()`

Returns all items where the value is not equivalent to `null`:

```php
// Some code
```

#### `whereNull()`

Returns all items where the value is the equivalent to `null`:

```php
// Some code
```
