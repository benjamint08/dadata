DaData
======


### Usage

```php
<?php
include __DIR__ . '/vendor/autoload.php';

class Animal extends \DaData\DaDataAbstractModel
{
    public $species = null;
    protected function getCollectionName()
    {
        return 'animals';
    }
}

$db = new \MongoDB\Client('mongodb+srv://my-user:my-password@cluster0.cuvj9.mongodb.net/my-db?shard-0&w=majority&readPreference=primary&appname=MongoDB%20Compass&retryWrites=true&ssl=true');
$database = $db->selectDatabase('my-db');

\DaData\DaDataDatabaseStore::getInstance()->setDatabase($database);

$animal = new Animal();
$animal->species = 'cat';
$animal->save();
```
