
Mit Hilfe dieses JobManagers können php-Scripte die z.B. in einer cronjob Tabelle stehen so ausgeführt werden,
dass der cronjob nicht doppelt gestartet wird, sondern je nach Erfolg oder Misserfolg der letzten Ausführen eine gewisse
Zeit vergehen muss.

Beispiel:
ein typischer cronjob wird durch diese Parameter definiert, welche beim ersten Aufruf so in der Datenbank gespeichert werden:

```php
$jobname = 'mycronjob'; // up to you
$groupname = 'imports'; // up to you
$status = JobManager\JobExecutor::ACTIVE;
$script = 'import/my_cronjob.php';
$minElapseOnSuccess = 1; // at least 1 minute has to elapse before restart after last successfull finish
$minElapseOnError = 60; // at least 60 minutes have to elapse before restart after last error
$description = 'Imports data';
```

Außerdem wird jede Ausführung eines cronjobs protokolliert und es enthält einen Logger, der im cronjob verwendet werden
kann und verschiedenen Arten von Logtypen schreiben kann.

Die Aufgaben des cronjobs müssen sich in einer eigenen Klasse mit start()-Methode befinden:
```php
$fakeDb = null;
$fakeResource = null;
$testString = "This conjob gets tracked with the JobManager library";
$my_cronjob = new JobManager\Example\MyCronJob($fakeDb, $fakeResource, $testString);
```

Für ein Beispiel siehe /Example/my_cronjob.php

