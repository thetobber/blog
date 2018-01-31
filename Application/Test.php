<?php
use Application\Message\Request;
use Application\Message\Response;
use Application\Message\ServerRequest;
use Application\Message\Stream;
use Application\Message\UploadedFile;
use Application\Message\Uri;


// $uri = Uri::fromString('https://user:pass@tobymw.dk:443/path/to/resource/?var=hello&arr[]=n1&arr[]=n2&arr[][]=hello#fragment');

$uri = new Uri(
    'https',
    'tobymw.dk',
    443,
    'user',
    'pass',
    'path/to/resource',
    'var=hello&arr[]=n1&arr[]=n2&arr[][]=hello',
    'fragment'
);
// $uri = new Uri(
//     'https',
//     'tobymw.dk',
//     443,
//     'user',
//     'pass',
//     'path/to/resource',
//     'var=hello&arr[]=n1&arr[]=n2&arr[][]=hello',
//     'fragment'
// );

var_dump(
    $uri,
    (string) $uri,
    $uri->getScheme(),
    $uri->getAuthority(),
    $uri->getUserInfo(),
    $uri->getHost(),
    $uri->getPort(),
    $uri->getPath(),
    $uri->getQuery(),
    $uri->getFragment()
);

echo $uri;
