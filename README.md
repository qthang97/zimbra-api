1. import seft sign certificate
copy to zimbra-api/cert<br />

2. run docker-compose up -d

Note: if jquery Ajax request fail. <br />
Check Example in https://github.com/zimbra-api/soap-api<br />
fix change all NAME() to NAME<br />
<br />
Example:<br />
$account = $api->getAccountInfo(new AccountSelector(AccountBy::NAME, $accountName));<br />
or<br />
$account = $api->getAccountInfo(new AccountSelector(AccountBy::NAME(), $accountName));
