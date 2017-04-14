gcputils-php
============

for Google Cloud Platform


Installation
------------

```
composer require otobank/gcputils
```


Usage
-----

### Cloud Storage

#### Generate signed URL to provide query-string auth'n to a resource

```
<?php

use GCPUtils\CloudStorage;

$storage = new CloudStorage();
$signedUrl = $storage->generateSignedUrl('/bucket/path/to/file.txt', time() + 86400);
```

