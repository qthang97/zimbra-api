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

//$username_admin = 'admin';
//$password_admin = 'Aa1230456';
isset($_POST['username_admin']) ? $username_admin = $_POST['username_admin'] : false;
isset($_POST['password_admin']) ? $password_admin = $_POST['password_admin'] : false;

$default_password_user = "H4n0i@2o89";
$message = [];
$email_arr = [];
$info = [];

//ham kiem tra domain da ton tai hay chua
function check_domain_status($domain)
{
    global $api; //user
    $get_domain_info = $api->getDomainInfo(new DomainSelector(DomainBy::NAME(), $domain), true);
    if ($get_domain_info->getDomain()->getName() == $domain) {
        return 1;
    }
    return 0;
}

//ham tao domain
function createDomain($domain)
{
    global $api;
    $domain = trim($domain);
    $check_domain_format = preg_match('/^[0-9a-zA-Z.-]+\.[a-z]{2,5}$/', $domain);
    //create domain
    if ($check_domain_format) {
        $create_domain = $api->createDomain($domain, []);
        //check domain created
        if ($create_domain->getDomain()->getName() == $domain) {
            $message = "Tạo tên miền thành công";
        } else {
            $message = "Tạo tên miền thất bại";
        }
    } else {
        $message = "Sai định dạng";
    }

    return $message;
}

//ham lay tat ca user trong domain
function getAllAccounts_domain($domain)
{
    global $api;
    $list_accounts = $api->getAllAccounts(NULL, new DomainSelector(DomainBy::NAME(), $domain))->getAccountList();
    return $list_accounts;
}

//ham lay quota used cua user
function getQuotaUsed($domain, $email)
{
    global $api;
    $quotaUsed = $api->getQuotaUsage($domain);
    $listQuotaUsed = $quotaUsed->getAccountQuotas();
    $totalUser = $quotaUsed->getSearchTotal();
    for ($i = 0; $i < $totalUser; $i++) {
        if ($listQuotaUsed[$i]->getName() == $email) {
            return $listQuotaUsed[$i]->getQuotaUsed();
        }
    }
}

//pr(getQuotaUsed("luckyplus.vn", "nhuanpv@luckyplus.vn"));

//ham kiem tra email da ton tai hay chua
function check_account_status($username_tmp)
{
    $username = $username_tmp;
    $domain = (explode("@", $username_tmp))[1];
    if (check_domain_status($domain) == 1) {
        $search_account = getAllAccounts_domain($domain);
        foreach ($search_account as $key => $value) {
            $name_tmp = $value->getName();
            if ($name_tmp == $username) {
                return 1;
            }
        }
    }
    return 0;
}

//ham in danh sach user
function print_accounts_list($domain, $server)
{
    $domain = trim($domain);
    $check_domain_format = preg_match('/^[0-9a-zA-Z.-]+\.[a-z]{2,5}$/', $domain);
    if ($check_domain_format) {
        if (check_domain_status($domain) == 1) {

            $list_accounts = getAllAccounts_domain($domain);
            $message = '<table class="table" id="list-users">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" >STT</th>
                    <th scope="col" >Email</th>
                    <th scope="col" >Status</th>
                    <th scope="col" >Action</th>
                    <th scope="col" >Quota Used</th>
                    <th scope="col" >Quota</th>
                </tr>
            </thead>';
            $i = 1;
            foreach ($list_accounts as $value) {
                $message = $message . '
                <tr>
                    <td scope="col" >' . $i . '</td>';
                $message = $message . '
                    <td scope="col" >' . $value->getName() . '</td>';

                $account_attr_list = $value->getAttrList();
                foreach ($account_attr_list as $key) {
                    //quota limited
                    if ($key->getKey() == "zimbraMailQuota") {
                        $message = $message . '
                        <td scope="col" >' . round((getQuotaUsed($domain, $value->getName()) / 1024 / 1024), 3) . 'MB</td>';
                        $message = $message . '<td scope="col" >' . (($key->getValue()) / 1024 / 1024) . 'MB </td>';
                    }

                    if ($key->getKey() == "zimbraAccountStatus") {
                        $message = $message . '
                        <td scope="col" >' . $key->getValue() . '</td>';
                        if ($key->getValue() == "active") {
                            $message = $message . '
                            <td scope="col" ><a href="#!" id="' . $value->getID() . '" domain="' . $domain . '" server="' . $server . '" action="closed" class="changestatus" >disable</a></td>';
                        } else {
                            $message = $message . '
                            <td scope="col" ><a href="#!" id="' . $value->getID() . '" domain="' . $domain . '" server="' . $server . '" action="active" class="changestatus" >enable</a></td>';
                        }
                    }
                }
                $i = $i + 1;
            }

            $message = $message . '
            </tr></table>';
        } else {
            $message = "<div class='col-12'>Không tìm thấy tên miền " . $domain . "</div>";
        }
    } else {
        $message = "Sai định dạng tên miền, tên miền chỉ chưa [0-9],[a-z]";
    }
    return $message;
}


function print_account($username, $domain, $server)
{
    $domain = trim($domain);
    $check_domain_format = preg_match('/^[0-9a-zA-Z.-]+\.[a-z]{2,5}$/', $domain);
    if ($check_domain_format) {

        if (check_domain_status($domain) == 1) {
            $list_accounts = getAllAccounts_domain($domain);
            $message = '<table class="table ">
        <thead class="thead-dark">
            <tr>
            <th scope="col" >STT</th>
            <th scope="col" >Email</th>
            <th scope="col" >Status</th>
            <th scope="col" >Action</th>
            <th scope="col" >Quota</th>
            </tr>
            </thead>';
            $i = 1;
            foreach ($list_accounts as $value) {
                if ($value->getName() == $username) {
                    $message = $message . '<tr><td scope="col" >' . $i . '</td>';
                    $message = $message . '<td scope="col" >' . $value->getName() . '</td>';
                    $account_attr_list = $value->getAttrList();
                    foreach ($account_attr_list as $key) {
                        if ($key->getKey() == "zimbraMailQuota") {
                            $message = $message . '<td scope="col" >' . (($key->getValue()) / 1024 / 1024) . '</td>';
                        }
                        if ($key->getKey() == "zimbraAccountStatus") {
                            $message = $message . '<td scope="col" >' . $key->getValue() . '</td>';
                            if ($key->getValue() == "active") {
                                $message = $message . '<td scope="col" ><a href="index.php?id=' . $value->getID() . '&domain=' . $domain . '&server=' . $server . '&acction=closed" class="changestatus" " id="disable" >disable</a></td>';
                            } else {
                                $message = $message . '<td scope="col" ><a href="index.php?id=' . $value->getID() . '&domain=' . $domain . '&server=' . $server . '&acction=active" class="changestatus" id="enable">enable</a></td>';
                            }
                        }
                    }
                    $i = $i + 1;
                }
            }

            $message = $message . "</tr></table>";
        } else {
            $message = "Không tìm thấy tên miền " . $domain;
        }
    } else {
        $message = "Sai định dạng tên miền, tên miền chỉ chưa [0-9],[a-z]";
    }
    return $message;
}

//ham tao tai khoan
function createAccount($username, $password)
{
    global $api;
    $displayName = (explode("@", $username))[0];
    $attrDisplayName = new Attr('displayName', (string)$displayName);
    if (check_account_status($username) == 0) {
        $create_account = $api->createAccount($username, $password, [$attrDisplayName]);
        if ($create_account->getAccount()->getName() == $username) {
            $message = "Tạo: email " . $username . " thành công";
        } else {
            $message = "Tạo: email " . $username . " thất bại, đã có lỗi xảy ra";
        }
    } else {
        $message = "Email " . $username . " đã tồn tại";
    }

    return $message;
}

//ham lay id cua user
function getID($username)
{
    global $api;
    $get_account = $api->getAccount(new AccountSelector(AccountBy::NAME(), $username));
    $id = $get_account->getAccount()->getId();
    return $id;
}

function resetPassword($id, $password)
{
    global $api;
    if (strlen($password) < 8) {
        $message = "Mật Khẩu của bạn quá ngắn, mật khẩu phải dài hơn 8 ký tự";
    } else {
        $result = $api->setPassword($id, $password);
        if ($result->getMessage() == "") {
            $message = "Reset mật khẩu thành công";
        }
    }
    return $message;
}

function changeQuota(string $id, $quota_input)
{
    global $api;
    $quota = $quota_input * 1024 * 1024;
    $attrQuota = new Attr('zimbraMailQuota', (string)$quota);
    $modify_account = $api->modifyAccount($id, [$attrQuota]);
    $result_change_quota = $modify_account->getAccount()->getAttrList();
    foreach ($result_change_quota as $value) {
        if (($value->getKey()) == "zimbraMailQuota") {
            $value->getValue() == $quota ? $message = $quota_input . "MB" : $message = "Thay đổi không thành công, đã có lỗi xảy ra";
        }
    }
    return $message;
}

function changeStatus(string $id, string $acction)
{
    global $api;
    if ($acction == "closed") {
        $attrStatus = new Attr('zimbraAccountStatus', "closed");
    } else {
        $attrStatus = new Attr('zimbraAccountStatus', "active");
    }
    $change_status = $api->modifyAccount($id, [$attrStatus]);

    return $change_status;
}



/*********************************************************************************************** */

//tao tai khoan
if (isset($_POST['create_account'])) {
    if ($_POST['email_input'] != "") {
        unset($email_arr);
        //kiem tra va gan du lieu
        isset($_POST['server']) ? $server = $_POST['server'] : false;
        isset($_POST['email_input']) ? $email_input = strtolower($_POST['email_input']) : false;
        isset($_POST['quota_input']) ? $quota_input = $_POST['quota_input'] : $quota_input = 500;

        //dang nhap
        $username_admin = $username_admin . "@" . $server;
        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);
        

        //tach chuoi email thanh array
        $para_to_arr = array_filter(explode(PHP_EOL, $email_input));
        foreach ($para_to_arr as $line) {

            $line_to_arr = array_filter(explode(" ", $line));
            foreach ($line_to_arr as $email) {
                //pr($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_arr[] = $email;
                }
            }
        }


        if (count($email_arr) != 0) {
            $tmp = '<table class="table ">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" >STT</th>
                    <th scope="col" >Email</th>
                    <th scope="col" >Status</th>
                </tr>
            </thead>';
            $i = 1;
            $info[0] = "Thông tin gửi KH:";
            $info[1]="1. Link truy cập vào địa chỉ mail: https://mail." .$server;
            $info[2]="2. Thông tin các tài khoản đăng nhập:";
            foreach ($email_arr as $email) {
                //xoa khoang trang truoc va sau cua chuoi
                $email = trim($email);


                //kiem tra dinh dang mail
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $tmp = $tmp . '<td scope="col" >Sai định dạng mail</td>';
                } else {
                    //tao ten mien va tra ket qua tao domain
                    $domain = (explode("@", $email))[1];

                    if (check_domain_status($domain) == 0) {
                        $tmp = $tmp . '<tr>
                        <td></td>
                            <td scope="col"><h3>' . $domain . '</h3></td>
                            <td scope="col" ><h3>' . createDomain($domain) . '</h3></td>
                            </tr>';
                        $i = 1;
                    }
                    $tmp = $tmp . '<tr><td scope="col" >' . $i . '</td>';
                    $tmp = $tmp . '<td scope="col" >' . $email . '</td>';

                    //tao tai khoan va tra ket qua tao tai khoan
                    $result_create_account = createAccount($email, $default_password_user);
                    $tmp = $tmp . '<td scope="col">' . $result_create_account . '</td>';
                    if($quota_input == 500){
                        $info[] = " - " .$result_create_account . " (500MB)";
                    }else{
                        //lay id cua user
                        $id = getID($email);
                        $info[] = " - Tạo: email " . $email . ' ('. changeQuota($id, $quota_input) .')';
                    }
                    
                    $i = $i + 1;
                }
            }
            $info[] = "Mật khẩu: " . $default_password_user;
            $message[] = $tmp . "</table>";
            $message[] = join("</br>",$info);
        } else {
            $message[] = "Nội dung không chứa email";
        }
    } else {
        $message[] = "Danh sách email không được bỏ trống";
    }
}

//dat lai mat khau
if (isset($_POST['reset_password'])) {
    if ($_POST['email_input'] != "") {
        unset($email_arr);
        //kiem tra va gan du lieu
        isset($_POST['server']) ? $server = $_POST['server'] : false;
        isset($_POST['email_input']) ? $email_input = strtolower($_POST['email_input']) : false;
        //dang nhap

        $username_admin = $username_admin . "@" . $server;
        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);

        //tach chuoi email thanh array
        $para_to_arr = array_filter(explode(PHP_EOL, $email_input));
        foreach ($para_to_arr as $line) {

            $line_to_arr = array_filter(explode(" ", $line));
            foreach ($line_to_arr as $email) {
                //pr($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_arr[] = $email;
                }
            }
        }

        if (count($email_arr) != 0) {
            $tmp = '<table class="table ">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" >STT</th>
                    <th scope="col" >Email</th>
                    <th scope="col" >Status</th>
                </tr>
            </thead>';
            $i = 1;
            $info[0] = "Thông tin gửi KH:";
            $info[1]="1. Link truy cập vào địa chỉ mail: https://mail." .$server;
            $info[2]="2. Thông tin các tài khoản đăng nhập:";
            foreach ($email_arr as $email) {
                //xoa khoang trang truoc va sau cua chuoi
                $email = trim($email);
                $tmp = $tmp . '<tr><td scope="col" >' . $i . '</td>';
                $tmp = $tmp . '<td scope="col" >' . $email . '</td>';

                //kiem tra dinh dang mail
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $tmp = $tmp . '<td scope="col" >Sai định dạng mail</td>';
                } else {
                    //kiem tra user co ton tai hay ko
                    if (check_account_status($email) == 1) {
                        //lay id cua user
                        $id = getID($email);

                        $password = $default_password_user;
                        $tmp = $tmp . '<td scope="col" >' . resetPassword($id, $password) . '</td>';
                        $info[] = " - Email: " . $email;  
                    } else {
                        $tmp = $tmp . '<td scope="col" >Không tìm thấy mail</td>';
                    }
                }
                $i = $i + 1;
            }
            $info[] = "Mật khẩu: " . $default_password_user;
            $message[] = $tmp . "</table>";
            $message[] = join("</br>",$info);
        } else {
            $message[] = "Nội dung không chứa email";
        }
    } else {
        $message[] = "Danh sách email không được bỏ trống";
    }
}

//thay doi quota
if (isset($_POST['change_quota'])) {
    if ($_POST['email_input'] != "") {
        unset($email_arr);
        //kiem tra va gan du lieu
        isset($_POST['server']) ? $server = $_POST['server'] : false;
        isset($_POST['email_input']) ? $email_input = strtolower($_POST['email_input']) : false;
        isset($_POST['quota_input']) ? $quota_input = $_POST['quota_input'] : false;

        //dang nhap
        $username_admin = $username_admin . "@" . $server;
        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);

        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        //tach chuoi email thanh array
        $para_to_arr = array_filter(explode(PHP_EOL, $email_input));
        foreach ($para_to_arr as $line) {

            $line_to_arr = array_filter(explode(" ", $line));
            foreach ($line_to_arr as $email) {
                //pr($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_arr[] = $email;
                }
            }
        }

        if (count($email_arr) != 0) {
            if ($quota_input != "") {
                $tmp = '<table class="table ">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" >STT</th>
                        <th scope="col" >Email</th>
                        <th scope="col" >Status</th>
                    </tr>
                </thead>';
                $i = 1;
                foreach ($email_arr as $email) {
                    //xoa khoang trang truoc va sau cua chuoi
                    $email = trim($email);
                    $tmp = $tmp . '<tr><td scope="col" >' . $i . '</td>';
                    $tmp = $tmp . '<td scope="col" >' . $email . '</td>';

                    //kiem tra dinh dang mail
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $tmp = $tmp . '<td scope="col" >Sai định dạng mail</td>';
                    } else {
                        //kiem tra user co ton tai hay ko
                        if (check_account_status($email) == 1) {

                            //lay id cua user
                            $id = getID($email);
                            $tmp = $tmp . '<td scope="col" >' . changeQuota($id, $quota_input) . '</td>';
                            $i = $i + 1;
                        } else {
                            $tmp = $tmp . '<td scope="col" >Không tìm thấy email</td>';
                        }
                    }
                }
                $message[] = $tmp . "</table>";
            } else {
                $message[] = "Vui lòng nhập dung lượng Quota";
            }
        } else {
            $message[] = "Nội dung không chứa email";
        }
    } else {
        $message[] = "Danh sách email không được bỏ trống";
    }
}

//list tat ca user co trong domain
if (isset($_POST['get_list_accounts'])) {
    if ($_POST['email_input'] != "") {
        isset($_POST['email_input']) ? $domain = strtolower($_POST['email_input']) : false;
        isset($_POST['server']) ? $server = $_POST['server'] : false;

        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        //dang nhap
        $username_admin = $username_admin . "@" . $server;
        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);
        $message[] = print_accounts_list($domain, $server);
    } else {
        $message[] = "Tên miền bạn đang bỏ trống";
    }
}

//thay doi trang thai tk
if (isset($_POST['changestatus'])) {
    $id = $_POST['id'];
    $server = $_POST['server'];
    $domain = $_POST['domain'];
    $action = $_POST['action'];

    //dang nhap
    $username_admin = $username_admin . "@" . $server;
    $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
    $api->auth($username_admin, $password_admin);

    $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

    changeStatus($id, $action);

    $message[] = print_accounts_list($domain, $server);
}

//enable tai khoan
if (isset($_POST['enable_account'])) {
    if ($_POST['email_input'] != "") {
        unset($email_arr);
        //kiem tra va gan du lieu
        isset($_POST['server']) ? $server = $_POST['server'] : false;
        isset($_POST['email_input']) ? $email_input = strtolower($_POST['email_input']) : false;

        //dang nhap
        $username_admin = $username_admin . "@" . $server;
        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);

        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        //tach chuoi email thanh array
        $para_to_arr = array_filter(explode(PHP_EOL, $email_input));
        foreach ($para_to_arr as $line) {

            $line_to_arr = array_filter(explode(" ", $line));
            foreach ($line_to_arr as $email) {
                //pr($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_arr[] = $email;
                }
            }
        }

        if (count($email_arr) != 0) {
            $tmp = '<table class="table ">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" >STT</th>
                                <th scope="col" >Email</th>
                                <th scope="col" >Status</th>
                                <th scope="col" >Action</th>
                            </tr>
                        </thead>';
            $i = 1;
            foreach ($email_arr as $email) {
                //xoa khoang trang truoc va sau cua chuoi
                $email = trim($email);
                $tmp = $tmp . '<tr><td scope="col" >' . $i . '</td>';
                $tmp = $tmp . '<td scope="col" >' . $email . '</td>';
                //kiem tra dinh dang mail
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $tmp = $tmp . '<td scope="col" colspan="2" >Sai định dạng email</td>';
                } else {
                    //kiem tra user co ton tai hay ko
                    if (check_account_status($email) == 1) {
                        //lay id cua user
                        $id = getID($email);

                        $result = changeStatus($id, "active")->getAccount();
                        $account_attr_list = $result->getAttrList();

                        foreach ($account_attr_list as $key) {
                            if ($key->getKey() == "zimbraAccountStatus") {
                                $tmp = $tmp . '<td scope="col" >' . $key->getValue() . '</td>';

                                if ($key->getValue() == "active") {
                                    $tmp = $tmp . '<td scope="col" ><a href="#!" id="' . $result->getID() . '" domain="' . $domain . '" server="' . $server . '" action="closed" class="changestatus" >disable</a></td></tr>';
                                } else {
                                    $tmp = $tmp . '<td scope="col" ><a href="#!" id="' . $result->getID() . '" domain="' . $domain . '" server="' . $server . '" action="active" class="changestatus" >enable</a></td></tr>';
                                }
                            }
                        }
                    } else {
                        $tmp = $tmp . '<td scope="col" colspan="2">Không tìm thấy email</td></tr>';
                    }
                    $i = $i + 1;
                }
            }
            $message[] = $tmp . "</table>";
        } else {
            $message[] = "Nội dung không chứa email";
        }
    } else {
        $message[] = "Danh sách email không được bỏ trống";
    }
}

//disable tai khoan
if (isset($_POST['disable_account'])) {
    if ($_POST['email_input'] != "") {
        unset($email_arr);
        //kiem tra va gan du lieu
        isset($_POST['server']) ? $server = $_POST['server'] : false;
        isset($_POST['email_input']) ? $email_input = strtolower($_POST['email_input']) : false;

        //dang nhap
        $username_admin = $username_admin . "@" . $server;
        $api = new AdminApi('https://mail.' . $server . ':7071/service/admin/soap');
        $api->auth($username_admin, $password_admin);

        $message[] = '<div class="blog-header py-3"><h3 class="blog-header-logo text-dark">Server: mail.' . $server . '</h3></div>';

        //tach chuoi email thanh array
        $para_to_arr = array_filter(explode(PHP_EOL, $email_input));
        foreach ($para_to_arr as $line) {

            $line_to_arr = array_filter(explode(" ", $line));
            foreach ($line_to_arr as $email) {
                //pr($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_arr[] = $email;
                }
            }
        }

        if (count($email_arr) != 0) {
            $tmp = '<table class="table ">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" >STT</th>
                                <th scope="col" >Email</th>
                                <th scope="col" >Status</th>
                                <th scope="col" >Action</th>
                            </tr>
                        </thead>';
            $i = 1;
            foreach ($email_arr as $email) {
                //xoa khoang trang truoc va sau cua chuoi
                $email = trim($email);
                $tmp = $tmp . '<tr><td scope="col" >' . $i . '</td>';
                $tmp = $tmp . '<td scope="col" >' . $email . '</td>';
                //kiem tra dinh dang mail
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $tmp = $tmp . '<td scope="col" colspan="2" >Sai định dạng email</td>';
                } else {
                    //kiem tra user co ton tai hay ko
                    if (check_account_status($email) == 1) {
                        //lay id cua user
                        $id = getID($email);

                        $result = changeStatus($id, "closed")->getAccount();
                        $account_attr_list = $result->getAttrList();

                        foreach ($account_attr_list as $key) {
                            if ($key->getKey() == "zimbraAccountStatus") {
                                $tmp = $tmp . '<td scope="col" >' . $key->getValue() . '</td>';

                                if ($key->getValue() == "active") {
                                    $tmp = $tmp . '<td scope="col" ><a href="#!" id="' . $result->getID() . '" domain="' . $domain . '" server="' . $server . '" action="closed" class="changestatus" >disable</a></td></tr>';
                                } else {
                                    $tmp = $tmp . '<td scope="col" ><a href="#!" id="' . $result->getID() . '" domain="' . $domain . '" server="' . $server . '" action="active" class="changestatus" >enable</a></td></tr>';
                                }
                            }
                        }
                    } else {
                        $tmp = $tmp . '<td scope="col" colspan="2">Không tìm thấy email</td></tr>';
                    }
                    $i = $i + 1;
                }
            }
            $message[] = $tmp . "</table>";
        } else {
            $message[] = "Nội dung không chứa email";
        }
    } else {
        $message[] = "Danh sách email không được bỏ trống";
    }
}

print_r(join("</br>", $message));
