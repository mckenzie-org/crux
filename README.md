Crux PowerTools Suite
=====================

The Crux PowerTools Suite is the heart of operations for any technology project.
The Crux system provides a complete out of the box and highly customizable solution that puts the control back into the hands of the management team reducing dependency on developers to do operational tasks. 

Crux provides the operational efficiency required for any data driven project.

Installation:
------------

Crux is only available to select clients of ETLOK Systems. If you want to use Crux, pleasee contact us at support@etlok.com

`composer require etlok/crux`

Publish Vendor Files
--

To publish configuration files, use the syntax below:

``php artisan vendor:publish --provider="Etlok\Crux\CruxServiceProvider" --tag="config"``

To publish views, use the syntax below:

``php artisan vendor:publish --provider="Etlok\Crux\CruxServiceProvider" --tag="views"``

To publish stubs, use the syntax below:

``php artisan vendor:publish --provider="Etlok\Crux\CruxServiceProvider" --tag="stubs"``

