<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="js/jquery.js"></script>
    <title>Zimbra-API</title>
</head>

<body>
    <div class="container">

        <div class="row form-group">
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-2">
                <label for="server">Chọn Server:</label>

                <select class="form-control" id="server" name="server">
                    <option value="mail.hcm.abc">test-site: mail.hcm.abc</option>
                    <option value="old.bizwebapi.com">old.bizwebapi.com</option>
                </select>
                <label for="username_admin">Username Admin:</label>
                <input type="text" id="username_admin" name="username_admin" class="form-control">
                <label for="password_admin">Password Admin:</label>
                <input type="password" id="password_admin" name="password_admin" class="form-control">
            </div>
            <div class="col-12 col-sm-12 col-md-5 col-lg-4 col-xl-6">
                <label for="email_input">Nhập danh sách các email/tên miền:</label>
                <textarea class="form-control" id="email_input" name="email_input" cols="50" rows="10"></textarea>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-2">
                <input class="form-check-input form-group" type="checkbox" id="enable_quota_input" name="enable_quota_input">
                <label class="form-check-label" for="enable_quota_input">Nhập Quota(MB):</label>
                <input class="form-control" type="text" id="quota_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" disabled name="quota_input">
                <br>
                <p>Default Password:</p>
                <select class="form-control" id="password" name="password">
                    <option value="H4n0i@2o89">H4n0i@2o89</option>
                    <option value="S@p0@6868">S@p0@6868</option>
                    <option value="S@p0@$6868">S@p0@$6868</option>
                </select>
            </div>
        </div>
        </br>
        <div>
            <button type="submit" class="btn btn-primary mb-2" id="create_account" name="create_account">Tạo tài khoản</button>
            <button type="submit" class="btn btn-primary mb-2" id="reset_password" name="reset_password">Reset mật khẩu</button>
            <button type="submit" class="btn btn-primary mb-2" id="get_list_accounts" name="get_list_accounts">Lấy danh sách user</button>
            <button type="submit" class="btn btn-primary mb-2" id="change_quota" name="change_quota">Thay đổi Quota</button>
            <button type="submit" class="btn btn-primary mb-2" id="enable_account" name="enable_account">Enable Account</button>
            <button type="submit" class="btn btn-primary mb-2" id="disable_account" name="disable_account">Disable Account</button>
        </div>


        </br>
        <div class="row" id="message">

        </div>
    </div>
</body>

</html>
