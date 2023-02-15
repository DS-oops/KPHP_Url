<?php

require_once "../Fragment.php";
require_once "../Path.php";

use KPurl\Fragment;
use KPurl\Path;
use KPurl\Query;
//echo parse_url('http://example.com/foo',PHP_URL_PATH);

//parse_str('http://example.com/foo',$data);
//$fragment = new Fragment('test?param=value');
//echo $fragment->instanceof('test?param=value', $fragment->getFragment());
$fragment = Fragment::from_string('jbv');
echo $fragment->getFragment();
$path = new Path('test');
$fragment->setPath($path);
echo($fragment->getPath());
//$query = new Query('param=value');
$fragment->setQuery(new Query('param=value'));
echo($fragment->getQuery());
echo ($fragment->getFragment());
//$fragment1=new Fragment($path='test1',$query=new Query('param=value1'));
//echo ($fragment1->get('query'));
//$fragment2=new Fragment();
//$fragment2->set('query',$query);
//echo $fragment2->getQuery();