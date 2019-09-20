# Presento

A data preparing and presenting package for PHP.

## Why Presento?

**Presento** is a simple but powerful tools for preparing and presenting data.
When we build an API based application, we need to _transform_ the data before _present_ it through the response. This package will make this task easier for you. 

Not clear enough? 

Don't worry, you'll get better idea from the [Usage examples](#usage).

### Requirements

```text
PHP >= 7.0
ext-json
```

## Installation

Install the package using composer:

```text
composer require nahid/presento
```

## Usage

**Presento** serves two important purposes. one is **Presentation** and another is **Transformation** of the data. 

Let's see some examples to understand how to use it.

We'll use the following data set to show the examples. Let's say we've this data set fetched from some data source and need to do some transformation or modifications before sending it to the response.

```php
$response = [
    "id" => 123456,
    "name" => "Nahid Bin Azhar",
    "email" => "talk@nahid.im",
    "type" => 1,
    "is_active" => 1,
    "created_at" => "2018-01-02 02:03:04",
    "updated_at" => "2018-01-02 02:03:04",
    "deleted_at" => "2018-01-02 02:03:04",
    "projects" => [
        [
            "id" => 1,
            "name" => "Laravel Talk",
            "url"   => "https://github.com/nahid/talk",
            "license" => "CC0",
            "created_at" => "2016-02-02 02:03:04"
        ],
        [
            "id" => 2,
            "name" => "JsonQ",
            "url"   => "https://github.com/nahid/jsonq",
            "license" => "MIT",
            "created_at" => "2018-01-02 02:03:04"
        ]
    ]
];
```

#### Simple Presentation Example
When sending this data to the API response, we only want to send the `id`, `name`, `email`, `type`, `is_active` and `projects`. 

We can simply do that by preparing a Presenter for this like following.

```php
// UserPresenter.php

class UserPresenter extends \Nahid\Presento\Presenter
{
    public function present() : array
    {
        return [
            'id',
            'name',
            'email',
            'type',
            'is_active',
            'projects',
        ];
    }
}
```

And you might already guessed how to use it, right? 

```php
$user = new UserPresenter($response);
dump($user->get());
```

It'd show something like this:

```php
[
    "id" => 123456,
    "name" => "Nahid Bin Azhar",
    "email" => "talk@nahid.im",
    "type" => 1,
    "is_active" => 1,
    "projects" => [
        [
            "id" => 1,
            "name" => "Laravel Talk",
            "url"   => "https://github.com/nahid/talk",
            "license" => "CC0",
            "created_at" => "2016-02-02 02:03:04"
        ],
        [
            "id" => 2,
            "name" => "JsonQ",
            "url"   => "https://github.com/nahid/jsonq",
            "license" => "MIT",
            "created_at" => "2018-01-02 02:03:04"
        ]
    ]
]
```

Pretty simple, right?

#### 'key' aliasing in Presentation example

Let's say you want to change some of the 'key' to something different, like the `id` key to `user_id` . 
How can you do that? 

Just do the following.

```php
// UserPresenter.php
class UserPresenter extends \Nahid\Presento\Presenter
{
    public function present() : array
    {
        return [
            'user_id' => 'id',
            'name',
            'email',
            'type',
            'is_active',
        ];
    }
}
```

This will format the data like following:

```php
[
    "user_id" => 123456,
    "name" => "Nahid Bin Azhar",
    "email" => "talk@nahid.im",
    "type" => 1,
    "is_active" => 1,
]
```


#### Deep traversing in Presentation example

You can easily dive deep and get data from a nested level by using `.` (dot) notation. 

Let's say you want to show the `name` of the first package as the `top_package` in your data. 

This is how you do it.

```php
// UserPresenter.php
class UserPresenter extends \Nahid\Presento\Presenter
{
    public function present() : array
    {
        return [
            'id',
            'name',
            'email',
            'type',
            'is_active',
            'top_package' => 'projects.0.name',
            'projects',
        ];
    }
}
``` 

This will format the data like this:

```php
[
    "id" => 123456,
    "name" => "Nahid Bin Azhar",
    "email" => "talk@nahid.im",
    "type" => 1,
    "is_active" => 1,
    "top_package" => "Laravel Talk",
    "projects" => [
        [
            "id" => 1,
            "name" => "Laravel Talk",
            "url"   => "https://github.com/nahid/talk",
            "license" => "CC0",
            "created_at" => "2016-02-02 02:03:04"
        ],
        [
            "id" => 2,
            "name" => "JsonQ",
            "url"   => "https://github.com/nahid/jsonq",
            "license" => "MIT",
            "created_at" => "2018-01-02 02:03:04"
        ]
    ]
]
```

Notice the `top_package` key in the data.


#### Simple Transformer Example

Let's say our UserPresenter is like this:

```php
// UserPresenter.php
class UserPresenter extends \Nahid\Presento\Presenter
{
    public function present() : array
    {
        return [
            'user_id' => 'id',
            'name',
            'email',
            'type',
            'is_active',
        ];
    }
}
``` 
And we want to show the `user_id` as _hashed_ value instead of an incremental integer value as it is in our database. That means we want to transform the `user_id`.

To do that we need to create a Transformer Class like this:

```php
// UserTransformer.php
class UserTransformer extends \Nahid\Presento\Transformer
{
    public function getUserIdProperty($value)
    {
        return md5($value);
    }
}
```

Notice that, as we will transform the `user_id` property, we named our transformer method as `getUserIdProperty`. So, if you want to transform the `name` property too, you need to just create another method in this class named `getNameProperty` and add the transformation logic inside it.

Now, we need to let know the _Presenter_ how to _Transform_ the data before presenting it. 

To do that, we need to add the following method in the `UserPresenter` class.

```php
// UserPresenter.php
public function transformer()
{
    return UserTransformer::class;
}
```

So, our final output would be:

```php
[
    "user_id" => "e10adc3949ba59abbe56e057f20f883e",
    "name" => "Nahid Bin Azhar",
    "email" => "talk@nahid.im",
    "type" => 1,
    "is_active" => 1,
]
```

Ain't it easy, mate?

#### Nested Presenter Example

You might notice that there is a collection of `projects` in our data set. If each `project` is a separate resource, you might have a separate Presenter for that. Like this:

```php
// ProjectPresenter.php
class ProjectPresenter extends \Nahid\Presento\Presenter
{
    public function present() : array
    {
        return [
            'id',
            'name',
            'url',
            'license',
            'created_at',
        ];
    }

    public function transformer()
    {
        return ProjectTransformer::class;
    }
}
```

Can you use this Presenter for each of the `projects` in the _Users_ data? 

Hell Yeah! Just do this:

```php
// UserPresenter.php
public function present() : array
{
    return [
        'user_id' => 'id',
        'name',
        'email',
        'type',
        'is_active',
        'projects' => [ProjectPresenter::class => ['projects']],
    ];
}
```

Now, each of the `project` in the list of `projects` in _Users_ will be presented as defined in the `ProjectPresenter`.


#### Base Data format conversion Example

As you have seen that, the data set we have used till now is a plain _Array_. But some times it might not be the case. You might need to work with something different, like **Eloquent Model** of Laravel framework. 
In that case, you can simply add a method called `convert` in your _Presenter_ to convert the Base data to an Array format.

Let's see an Example:

```php
// UserPresenter.php
public function convert($data)
{
    if ($data instanceof Model) {
        return $data->toArray();
    }

    return $data;
}
```

That's it. 

