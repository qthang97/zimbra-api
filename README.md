1. import seft sign certificat
copy to zimbra-api/cert

2. run docker-compose up -d

Note: if jquery Ajax request fail. 
Check Example in https://github.com/zimbra-api/soap-api
fix change all NAME() to NAME

Example:
$account = $api->getAccountInfo(new AccountSelector(AccountBy::NAME, $accountName));\n
or\n
$account = $api->getAccountInfo(new AccountSelector(AccountBy::NAME(), $accountName));
