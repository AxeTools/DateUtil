<h1 align="center">AxeTools/DateUtil</h1>

<p align="center">
    <strong>This is a php class trait that will provide methods for performing simple bitwise operations.</strong>
</p>

<p align="center">
    <a href="https://github.com/AxeTools/DateUtil"><img src="https://img.shields.io/badge/source-AxeTools/DateUtil-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/AxeTools/DateUtil.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/AxeTools/DateUtil/blob/1.x/LICENSE"><img src="https://img.shields.
io/packagist/l/AxeTools/DateUtil.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/AxeTools/DateUtil/actions/workflows/php.yml"><img src="https://img.shields.io/github/actions/workflow/status/AxeTools/DateUtil/php.yml?branch=1.x&logo=github&style=flat-square" alt="Build Status"></a>
</p>

This project uses [Semantic Versioning][].

The `DateUtil` gives simple methods for creating relative dates, absolute dates and utilities for creating holidays and determining if the current day is a holiday or not.

Some Examples.

```php
<?php
// The first Monday of the Month of September for the current year
$labor_day = DateTimeUtil::RelativeDateTime(9, DayOfWeek::MONDAY, Week::FIRST);

// Get Christmas day, December 25th for the current year
$christmas_day = DateTimeUtil::AbsoluteDateTime(12, 25);

// Get the last day of April for 2022
$last_day_april = DateTimeUtil::AbsoluteDateTime(4, DateTimeUtil::RELATIVE_LAST_DAY);

// Get the last day of the current month
$last_day_this_month = DateTimeUtil::AbsoluteDateTime(null, DateTimeUtil::RELATIVE_LAST_DAY);

// Get an array of Holiday objects for the US Federal Holidays from 2001
$holidays_2001 = DateTimeUtil::usFederalHolidays(2001);
```

## Installation

The preferred method of installation is via [Composer][]. Run the following command to install the package and add it as
a requirement to your project's `composer.json`:

```bash
composer require axetools/dateutil
```

[composer]: http://getcomposer.org/
[semantic versioning]: https://semver.org/spec/v2.0.0.html
[bitwise operators]: https://www.php.net/manual/en/language.operators.bitwise.php