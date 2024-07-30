# Import Inline Media

Requires Import WP: 2.14.1

**Version: 0.0.1**

## Description

Import Media from imported post content into the wordpress media library

## FAQ 

### What fields can does the inline media work with.

By default it is only the post_content field, but extra fields can be set using:

```php
add_filter('iwp/inline-media/template-fields', function($fields){
    $fields[] = 'description';
    return $fields;
});
```

### Can i restrict which urls should be imported from.

```php
add_filter('iwp/inline-media/source', function($allowed, $url){

    // if attachment doesnt originate from placehold.co, dont download.
    if (strpos($url, "https://placehold.co/") !== 0) {
        return false;
    }

    return $allowed;
}, 10, 2);
```

### Can i restrict which types of files can be imported.

```php
add_filter('iwp/inline-media/source', function($allowed, $url){

    // if attachment doenst have the extention
    if (preg_match('/\.(?:png|jpe?g|gif|ico|webp|bmp|svg)$/', $url) !== 1) {
        return false;
    }

    return $allowed;
}, 10, 2);
```

### What html elements does the inline media check

The inline media addon reads <img src=""/>, but this can be extended using the following filters, e.g. to also search <a href="" />.

```php
add_filter('iwp/inline-media/input-tags', function($tags){
    $tags[] = 'a';
    return $tags;
});
```

```php
add_filter('iwp/inline-media/input-attributes', function($tags){
    $tags[] = 'href';
    return $tags;
});
```

## Changelog
