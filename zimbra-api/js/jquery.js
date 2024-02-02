var url = 'xu-ly-du-lieu.php';
$(document).ready(function () {
    $("#enable_quota_input").click(function () {
        if ($(this).is(":checked")) {
            $("#quota_input").removeAttr("disabled");
            $("#quota_input").focus();
        } else {
            $("#quota_input").attr("disabled", "disabled");
            $("#quota_input")[0].selectedIndex = 0;
        }
    });

    //xoa cac du lieu
    function clear_input() {
        $("#server")[0].selectedIndex = 0;
        $('#email_input').val("");
        $("#enable_quota_input").prop("checked", false);
        $("#quota_input").attr("disabled", "disabled");
        $('#quota_input').val("");
    };

    //tao tai khoan
    $('#create_account').click(function () {
        server = $('#server').val();
        email_input = $.trim($('#email_input').val());
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();
        $.ajax({
            type: "POST",
            url: url,
            data: {
                create_account: "create_account",
                email_input: email_input,
                server: server,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //reset mat khau
    $('#reset_password').click(function () {
        server = $.trim($('#server').val());
        email_input = $.trim($('#email_input').val());
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();
        $.ajax({
            type: "POST",
            url: url,
            data: {
                reset_password: 'reset_password',
                email_input: email_input,
                server: server,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //thay doi quota
    $('#change_quota').click(function () {
        server = $('#server').val();
        email_input = $.trim($('#email_input').val());
        quota_input = $('#quota_input').val();
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();

        $.ajax({
            type: "POST",
            url: url,
            data: {
                change_quota: "change_quota",
                email_input: email_input,
                server: server,
                quota_input: quota_input,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //list danh sach user
    $('#get_list_accounts').click(function () {
        server = $('#server').val();
        email_input = $.trim($('#email_input').val());
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();

        $.ajax({
            type: "POST",
            url: url,
            data: {
                get_list_accounts: "get_list_accounts",
                email_input: email_input,
                server: server,
                username_admin: username_admin,
                password_admin: password_admin

            },
            success: function (data) {
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //enable accounts
    $('#enable_account').click(function () {
        var server, email_input, enable_account;
        server = $('#server').val();
        email_input = $.trim($('#email_input').val());
        enable_account = "enable_account";
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                email_input: email_input,
                server: server,
                enable_account: enable_account,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //disable accounts
    $('#disable_account').click(function () {
        var server, email_input, disable_account;
        server = $('#server').val();
        email_input = $.trim($('#email_input').val());
        disable_account = "disable_account";
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                email_input: email_input,
                server: server,
                disable_account: disable_account,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                //console.log(data);
                $("#message").html(data);
            }
        });
        clear_input();
    });

    //thay doi trang thay tai khoan
    $("#message").on('click', '.changestatus', function () {

        var arr;
        var currentRow = $(this).closest("tr");
        var status = currentRow.find("td:eq(2)");
        var a_tag = currentRow.find('a');
        var id = a_tag.attr('id');
        var domain = a_tag.attr('domain');
        var server = a_tag.attr('server');
        var action = a_tag.attr('action');

        //console.log(id+domain+server);
        username_admin = $('#username_admin').val();
        password_admin = $('#password_admin').val();
        $.ajax({
            type: "POST",
            url: url,
            data: {
                changestatus: "changestatus",
                id: id,
                domain: domain,
                server: server,
                action: action,
                username_admin: username_admin,
                password_admin: password_admin
            },
            success: function (data) {
                arr = data.split(",");
                // console.log(arr);
                status.html(arr[0]);
                a_tag.attr('action', arr[1]);
                a_tag.html(arr[2]);
                // $(this).attr('action', arr[1]);
            }
        });
        clear_input();
    });
});
