# Core UX - Laravel Library
A personal set of tools to start building APIs or Laravel Apps

## Features

- Standard API Response
- Request Time

### Standard API Response
Adding meta data helps when debugging from Webhook logs
```json
{
    "status": "success",
    "data": "This could be anything including arrays and object",
    "errors": [],
    "meta": {
        "httpCode": 200,
        "milliseconds": 120,
        "timestamp": "2023-04-07 17:19:05"
    },
    "pagination": null
}
```
#### Usage
In your App/Http/Controller add ApiResponse as a trait
```php
use \Coreux\Lib\API\Traits\ApiResponse;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;
}
```
Now you can respond return this as a successful return
```php
return $this->apiReturn($users);
```
You can also use Pagination from Eloquent
```php
return $this->apiReturnPaginated($usersPaginated,$transformer=null);
```
Or you can return an error and specify the code
```php
return $this->apiError($errors,$code=400);
```
### Request Time
By defining at the following line in your index.php (App entry) the API response will be able to return the milliseconds your code ran for
```php
define('APP_START', microtime(true));
```