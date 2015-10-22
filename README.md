# Clockwork for the Lithium framework.

[Original Project](https://github.com/itsgoingd/clockwork)

[Chrome Extension](https://chrome.google.com/webstore/detail/clockwork/dmggabnehkmmfmdffgajcflpdjlnoemp)

## Usage

```php
# bootstrap.php
\lithium\core\Libraries::add('li3_clockwork');
\li3_clockwork\extensions\StaticClockwork::getInstance()->setStorage(
    new \Clockwork\Storage\FileStorage("/path/to/tmp")
);
```