# LanguageDetection
php Language Detection Class

The class matches the `availableLanguages` configuration with the browser’s Accept-Language header and returns the preferred.

## Usage


### find preferred language
The method `findLanguage` returns an iso2 language key.
* From cookie OR
* Matched by the `Accept-Language` header from browser. OR
* Returns the fallbackLanguage

```php
$availableLanguages = array( 'nl', 'de', 'cz', 'pl' );
$languageDetection = new \NR\Utilities\LanguageDetection( $availableLanguages );
$iso2 = $languageDetection->findLanguage();
``` 


### set language fallback
set a language fallback in constructor (default is 'en').

```php
$languageDetection = new \NR\Utilities\LanguageDetection(
   $availableLanguages,
   'de'
);

// or set later with
$languageDetection->fallbackLanguageIso2 = 'de';
```

### get the Accept-Language header from browser
The array is sorted by the header’s quality attribute (e.g. ;q=0.8).

```php
$languageDetection->getAcceptedLanguage();

/* e.g.
Array
(
    [0] => de-DE
    [1] => de
    [2] => en-US
    [3] => en
)
*/
```

## Cookie

If `$cookieSupport` is true (default), the language value can be stored as a cookie. The method `findLanguage` prefers the cookie value if set.


### set available language to cookie
If the language value ('de') is contained in `availableLanguages`, the value will be saved in the cookie.

```php
$languageDetection->setLanguage('de');

echo $_COOKIE['LanguageDetection']
// same as 
echo $languageDetection->findLanguage();
```


### delete cookie
The cookie's expiration time is set to `-1` and the global var `$_COOKIE[…]` is unset.

```php
$languageDetection->unsetLanguage();
```

### configuration options for cookie

```php
// defaults
$languageDetection->cookieSupport = true;

$languageDetection->cookieName = 'LanguageDetection';
$languageDetection->cookieExpire = 0;
$languageDetection->cookiePath = '/';
$languageDetection->cookieDomain = '';
$languageDetection->cookieSecure = false;
$languageDetection->cookieHttponly = false;
```