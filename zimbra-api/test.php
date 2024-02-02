<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

//call class Admin API
use Zimbra\Admin\AdminApi;

//call class get userinfo
use Zimbra\Common\Enum\AccountBy;
use Zimbra\Common\Struct\AccountSelector;

use Zimbra\Admin\Struct\Attr;

//call class get domain info
use Zimbra\Admin\Struct\DomainSelector;
use Zimbra\Common\Enum\DomainBy;

function pr($data)
{
    echo "<pre>";
    print_r($data); // or var_dump($data);
    echo "</pre>";
}

$usertest = '';
$domain = 'a.com';
$server = "hcm.abc";
try{
    $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
    $api->auth("admin@hcm.abc", "Aa1230456");
}catch(Exception $e){
    echo "Username, Password Wrong";
}

$account = $api->getAccount(new AccountSelector(AccountBy::NAME, "a@a.com"));
// $account = $api->getAccountInfo(new AccountSelector(AccountBy::NAME(), "a@a.com"));
// $account = $api->getDomainInfo(new DomainSelector(DomainBy::NAME, 'a.com'), true);

// $account = $api->ge;
// echo "xinchaof";   
pr($account);
