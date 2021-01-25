# A little more secure

WordPress plugin that should protect wp-login.php form from brute force attacks.

## Custom security parameter

On wp-login.php there will be a JavaScript redirect if a specific GET parameter is missing. This paramter can be customized with the `a_little_more_secure_get_param_name` filter.

```php
add_filter('a_little_more_secure_get_param_name', function($name){
    $name = "new_param_name";
    return $name;
});
``` 

## Redirect waiting time

If the wp-login.php is requested without the secure parameter there will be a JavaScript redirect. The waiting time can be customized with the `a_little_more_secure_redirect_wait_seconds` filter.

```php
add_filter('a_little_more_secure_redirect_wait_seconds', function($seconds){
    $seconds = 10;
    return $seconds;
});
```
