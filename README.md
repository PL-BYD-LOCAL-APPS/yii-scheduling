Schedule extension for Yii 1.1
==============================

This extension is the port of omnilight/yii2-scheduling (https://github.com/omnilight/yii2-scheduling)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require anonymous-php/yii-scheduling "*"
```

or add

```json
"anonymous-php/yii-scheduling": "*"
```

to the `require` section of your composer.json.

Description
-----------

This project is inspired by the Laravel's Schedule component and tries to bring it's simplicity to the Yii framework.
Quote from Laravel's documentation:

```
In the past, developers have generated a Cron entry for each console command they wished to schedule.
However, this is a headache. Your console schedule is no longer in source control,
and you must SSH into your server to add the Cron entries. Let's make our lives easier.
```

After installation you have to create console command extending `Anonymous\Scheduling\ScheduleCommand` class 
and implement `schedule()` method with your jobs inside:

```php
<?php

class ScheduleCommand extends \Anonymous\Scheduling\ScheduleCommand
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('foo')->monthly();
    }
}
```

When setup will be finished just put single line into crontab:

```
* * * * * php /path/to/yii yii schedule/run 1>> /dev/null 2>&1
```

You can put your schedule into the `schedule.php` file, or add it withing bootstrapping of your extension or
application

Schedule examples
-----------------

This extension is support all features of Laravel's Schedule, except environments and maintance mode.

**Scheduling Closures**

```php
$schedule->call(function()
{
    // Do some task...

})->hourly();
```

**Scheduling Terminal Commands**

```php
$schedule->exec('composer self-update')->daily();
```

**Running command of your application**

```php
$schedule->command('migrate')->cron('* * * * *');
```

**Frequent Jobs**

```php
$schedule->command('foo')->everyFiveMinutes();

$schedule->command('foo')->everyTenMinutes();

$schedule->command('foo')->everyThirtyMinutes();
```

**Daily Jobs**

```php
$schedule->command('foo')->daily();
```

**Daily Jobs At A Specific Time (24 Hour Time)**

```php
$schedule->command('foo')->dailyAt('15:00');
```

**Twice Daily Jobs**

```php
$schedule->command('foo')->twiceDaily();
```

**Job That Runs Every Weekday**

```php
$schedule->command('foo')->weekdays();
```

**Weekly Jobs**

```php
$schedule->command('foo')->weekly();

// Schedule weekly job for specific day (0-6) and time...
$schedule->command('foo')->weeklyOn(1, '8:00');
```

**Monthly Jobs**

```php
$schedule->command('foo')->monthly();
```

**Job That Runs On Specific Days**

```php
$schedule->command('foo')->mondays();
$schedule->command('foo')->tuesdays();
$schedule->command('foo')->wednesdays();
$schedule->command('foo')->thursdays();
$schedule->command('foo')->fridays();
$schedule->command('foo')->saturdays();
$schedule->command('foo')->sundays();
```

**Only Allow Job To Run When Callback Is True**

```php
$schedule->command('foo')->monthly()->when(function()
{
    return true;
});
```

**E-mail The Output Of A Scheduled Job**

```php
$schedule->command('foo')->processOutput(function ($textBody, $app) {
    if (trim($textBody) === '' ) {
        return;
    }

    $app->mailer
        ->compose()
        ->setTextBody($textBody)
        ->setSubject($this->getEmailSubject())
        ->setTo($addresses)
        ->send();
});
```

**Preventing Task Overlaps**

```php
$schedule->command('foo')->withoutOverlapping();
```
Used by default `FileMutex` or 'mutex' application component (https://github.com/yiisoft/mutex)

**Running Tasks On One Server**

>To utilize this feature, you must config mutex in the application component, except the FileMutex:  `yii\mutex\MysqlMutex`,`yii\mutex\PgsqlMutex`,`yii\mutex\OracleMutex` or `yii\redis\Mutex`. In addition, all servers must be communicating with the same central db/cache server.

Below shows the redis mutex demo:

```php
'components' => [
    'mutex' => [
        'class' => 'yii\redis\Mutex',
        'redis' => [
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ]
    ],
],
```

```php
$schedule->command('report:generate')
                ->fridays()
                ->at('17:00')
                ->onOneServer();
```

Using addition functions
------------------------

If you want to use `thenPing` method of the Event, you should add the following string to the `composer.json` of your app:
```
"guzzlehttp/guzzle": "~5.0"
```

Note about timezones
--------------------

Please note, that this is PHP extension, so it use timezone defined in php config or in your Yii's configuration file,
so set them correctly.
