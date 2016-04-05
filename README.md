# Apiranha

Apiranha is a library that makes consuming APIs easier and faster. Some of the inspiration for Apiranha comes from [Retrofit](square.github.io/retrofit/)

[![Travis Ci](https://travis-ci.org/kix/apiranha.svg?branch=master)](https://travis-ci.org/Fakerino/Fakerino)
[![Code Climate](https://codeclimate.com/github/kix/apiranha/badges/gpa.svg)](https://codeclimate.com/github/kix/apiranha)
[![Coverage Status](https://coveralls.io/repos/github/kix/apiranha/badge.svg?branch=master)](https://coveralls.io/github/kix/apiranha?branch=master)

## Quick start

Make sure you have Composer installed, then run this in your project folder:

```
composer require kix/apiranha='dev-master'
```

Next, declare some API endpoints using annotations:

```php


interface MyBookApi
{
   /**
    *
    */
    public function listBooks();
}

# States

1. Before request
2. Request
