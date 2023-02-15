<?php
require_once "../Fragment.php";
require_once "../Parser.php";



use KPurl\Url;

//$url = new Url();
//$url->setUrl('https://host.com:443/path with spaces?param1 with spaces=value1 with spaces&param2=value2#fragment1/fragment2 with spaces?param1=value1&param2 with spaces=value2 with spaces');
//echo($url->getQuery());
//$url = Url::parse('http://jwage.com');
//$url->set('port', '443');
//$url->set('scheme', 'https');
//$url->setQuery('param1');
//$url->setQuery('param2');
//$url = new Url('http://jwage.com/about?param=value#fragment');
//$url->join(new Url('http://about.me/jwage'));
//$url = new Url('https://example.com');
//$url->join(new Url('//code.jquery.com/jquery-3.10.js'));
//$url = new Url('https://user:pass@jwage.com:443');
//$url1 = new Url('http://jwage.com');
//$url2 = new Url('/about/me');
//$url->set('port','433');
//echo $url1->isAbsolute() ? 'true' : 'false';
//echo $url2->isAbsolute() ? 'true' : 'false';
//echo (string)$url1->isAbsolute();
//echo (string)$url2->isAbsolute();
//echo $url->getNetloc();
//$url = new Url('http://jwage.com/about?query=value');
//echo $url->getData('resource');
//$url = new Url('http://user:pass@jwage.com');
//echo $url->getData('user');
//echo $url->getData('pass');
//$urls = Url::extract("test\nmore test https://google.com ftp://jwage.com ftps://jwage.com http://google.com\ntesting this out http://jwage.com more text https://we-are-a-professional-studio-of.photography");
//echo (string) ($urls[0]);
//$url = new Url('http://jwage.com');
////$url->setPath( new Path('about'));
////$url->setQuery( new Query('param=value'));
////$url->setFragment(Fragment::from_path(new Path('about')));
////$url->fragment->setQuery(new Query('param=value'));
////echo $url->getUrl();
//$url = new Url('/path1');
//echo $url->getUrl();
$_SERVER['HTTP_HOST'] = 'jwage.com';
$_SERVER['SERVER_PORT'] = 80;
$_SERVER['REQUEST_URI'] = '/about';
$url = Url::fromCurrent();
echo $url->getUrl();