/**
 * This script is responsible for the logic behind the user management screen.
 * 
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
(function (OC, window, $) {
    'use strict';
    let users;
    let changedUsers = [];
    let groups = [];

    /**
     * Returns an array of objects holding the values for each changed User
     */
    function getChanges() {
        let updates = [];
        changedUsers.forEach(uid => {
            let row = $("#" + uid);
            let secsignid = row.find(".ssid input").val();
            let enabled = row.find("#enabled input").is(':checked') ? 1 : 0;
            updates.push({
                uid: uid,
                secsignid: secsignid,
                enabled: enabled
            });
        });
        return updates;
    }

    /**
     * Saves all changes made to the database and updates the table.
     */
    function saveChanges() {
        let changes = getChanges();
        $.post(OC.generateUrl('apps/secsignid/ids/update/'), {
                data: changes
            },
            function (data) {
                changedUsers = [];
                alert("Successfully updated users!");
                showTable(data);
                $("#edited").hide();
                showChanges();
            }).fail(function (data) {
            console.log("An error occurred while saving changes");
        });
    }

    /**
     * Returns the html string for a table row for the given user
     * @param {Object} user the user a row will be created for
     */
    function addUserRow(user) {
        let html = '';
        let displayname = user.displayname == null ? user.uid : user.displayname;
        let checked = user.enabled == 1 ? 'checked' : '';
        let secsignid = user.secsignid == null ? "" : user.secsignid;
        html += "<tr id='" + user.uid + "'>";
        html += "   <td>" + user.uid + "</td>";
        html += "   <td class='displayname'>" + displayname + "</td>";
        html += "   <td class='center ssid'><input type='text' placeholder='None' value='" + secsignid + "'></td>";
        html += "<td id='enabled' class='center'>";
        html += "<input type='checkbox' class='checkbox' " + checked + " id='cb" + user.uid + "'>"
        html += "<label class='enforce' for='cb" + user.uid + "'></label></td>";
        html += "<td id='check' class='icon-checkmark' hidden></td>";
        html += "</tr>";
        return html;
    }


    /**
     * Updates the indicator showing how many users were modified
     */
    function showChanges() {
        let changes = $('#total_changes');
        let val_string = changes.text().replace(/[0-9]/g, '');
        changes.text(val_string + changedUsers.length);
    }


    /**
     * Is called when the enabled checkbox is toggled
     * @param {string} val the new value of the checkbox
     * @param {Object} user the user corresponding to the row clicked
     */
    function changedEnabled(val, user) {
        let user_enabled = user.enabled == 1;
        if (user_enabled == val && $("#" + user.uid).find(".ssid input").val() == user.secsignid) {
            // All changes were reverted
            changedUsers.splice(changedUsers.indexOf(user.uid), 1);
            $("#" + user.uid).find("#check").hide();
            if (changedUsers.length == 0) {
                $("#edited").hide();
                showChanges();
            }
        } else if (!changedUsers.includes(user.uid)) {
            // User was changed for the first time
            changedUsers.push(user.uid);
            $("#" + user.uid).find("#check").show();
            if (changedUsers.length == 1) {
                $("#edited").show();
            }
            showChanges();
        }
    }

    /**
     * Is called when a SecSign ID is modified. 
     * @param {string} val the new value of the secsignid field
     * @param {Object} user the user corresponding to the modified row
     */
    function changedID(val, user) {
        let row = $("#" + user.uid);
        let enabled = row.find("#enabled input").is(':checked') ? 1 : 0;
        let secsignid = user.secsignid == null ? '' : user.secsignid;
        let user_enabled = user.enabled == null ? false : user.enabled;
        if (val == secsignid && user_enabled == enabled ||
            val == secsignid && user.secsignid == null) {
            // All changes were reverted
            changedUsers.splice(changedUsers.indexOf(user.uid), 1);
            $("#" + user.uid).find("#check").hide();
            if (changedUsers.length == 0) {
                $("#edited").hide();
            }
            showChanges();
            if (user.secsignid == null && user.enforced != "1") {
                let check = row.find("#enabled input");
                check.prop("disabled", true);
                check.prop("checked", false);
            }
        } else {
            if (!changedUsers.includes(user.uid)) {
                // User was changed for the first time
                changedUsers.push(user.uid);
                $("#" + user.uid).find("#check").show();
                if (changedUsers.length == 1) {
                    $("#edited").show();
                }
                showChanges();
                if (user.secsignid == null) {
                    row.find("#enabled input").prop("disabled", false);
                }
            }
        }
    }

    /**
     * Creates the html for a table for a given array of users
     * @param {[]} data the array of users
     */
    function showTable(data) {
        users = data;
        let html = '';
        initGroups(data);
        data.forEach(user => {
            html += addUserRow(user);
        });
        $("#tbody").html(html);
        users.forEach(user => {
            let row = $("#" + user.uid);
            if (user.enforced === "1") {
                row.find(".checkbox").prop("disabled", true);
                row.find(".checkbox").prop("checked", true);
                row.find("label").html("2FA enforced");
            }
            if (user.enabled == 1 && !user.secsignid || user.enforced) {
                if (!user.secsignid) {
                    row.find(".ssid").append("<span class='icon-error '></span>");
                    $("#enforced_warning").show();
                }
            }
            row.find(".ssid input").change(function () {
                changedID($(this).val(), user);
            });
            row.find("#enabled").change(function () {
                changedEnabled($('input', this).is(":checked"), user);
            });
        });
    }

    /**
     * Initiates group list and filters
     * 
     * @param {array} users
     */
    function initGroups(users) {
        users.forEach(user => {
            if (user.groups.length === 0) {
                if (!groups['no group']) {
                    groups['no group'] = [];
                }
                groups['no group'].push(user);
            } else {
                user.groups.forEach(group => {
                    if (!groups[group]) {
                        groups[group] = [];
                    }
                    groups[group].push(user);
                });
            }
        });
        console.log(groups);
        Object.keys(groups).forEach(group => {
            let html = `<option value="${group}">${group}</option>`
            console.log(html);
            $('#sec_select_group').append(html);
        });
    }

    /**
     * Filters the user list by group
     * 
     * @param {string} group 
     */
    function getGroupList(group) {
        if (group === 'All groups') {
            $('tbody tr').show();
        } else {
            $('tbody tr').hide();
            groups[group].forEach(user => {
                $('tr#' + user.uid).show();
            });
        }
    }

    /**
     * Retrieves an array of all users from the server then shows the corresponding table
     */
    function getUsers() {
        $.get(OC.generateUrl('/apps/secsignid/ids/users/'),
            function (data) {
                showTable(data);
                $(".lds-roller").hide();
                $("#table").show();
                $("#save_changes").click(function () {
                    saveChanges();
                })
            });
    }

    function getPermissions() {
        $.get(OC.generateUrl('/apps/secsignid/allowEdit/'),
            function (allow) {
                let check = $("#allow_user_enable");
                check.prop("disabled", false);
                check.prop("checked", allow);
                check.change(function () {
                    $("#save_allow_enable").show();
                })
            });
    }

    function save_allow_enable() {
        let check = $("#allow_user_enable");
        let save = $("#save_allow_enable");
        $.post(OC.generateUrl("/apps/secsignid/allowEdit/"), {
                allow: check.prop("checked")
            },
            function () {
                save.html("Saved");
                save.fadeOut(3000);
            }).fail(function () {
            alert("An error occured while saving. Try again");
            check.prop("checked", !check.checked);
        });
    }

    function save_onboarding() {
        $.post(OC.generateUrl("/apps/secsignid/onboarding/"), {
            data: {
                enabled: $("#enable_onboarding").prop("checked"),
                suffix: $("#onboarding_suffix").val()
            }
        }).success(function () {
            $("#save_onboarding").html("Saved");
            $("#save_onboarding").fadeOut(3000);
        }).fail(function () {
            alert("An error has occured, please try again");
        });
    }

    function openTab(evt, tabName) {
        $(".tabcontent").css("display", "none");
        $("#app-navigation li a").removeClass("selected");

        $("#" + tabName).css("display", "block");
        evt.addClass("selected");
    }

    function save_server() {
        $.post(OC.generateUrl('/apps/secsignid/server/'), {
            server: {
                server: $("#ssid_server").val(),
                serverport: $("#ssid_server_port").val(),
                fallback: $("#ssid_fallback").val(),
                fallbackport: $("#ssid_fallback_port").val()
            }
        }).success(function () {
            $("#save_server").html("Saved");
            $("#save_server").fadeOut(3000);
        }).fail(function () {
            alert("An error has occured, please try again");
        })
    }

    function save_server_mobile() {
        $.post(OC.generateUrl('/apps/secsignid/server/mobile/'), {
            server: $("#ssid_server_mobile").val()
        }).success(function () {
            $("#save_server_mobile").html("Saved");
            $("#save_server_mobile").fadeOut(3000);
        }).fail(function () {
            alert("An error has occured, please try again");
        })
    }

    function getOnboarding() {
        let check = $("#enable_onboarding");
        let input = $(".onboarding_input");
        let suffix = $("#onboarding_suffix");
        let save = $("#save_onboarding");
        $.get(OC.generateUrl("/apps/secsignid/onboarding/"))
            .success(function (data) {
                check.prop("checked", data.enabled);
                suffix.val(data.suffix);
                $("#onboarding_example").html("Schema example: john.doe@" + suffix.val())
                if (data.enabled) {
                    input.show();
                }
            });
        check.change(function () {
            save.val("Save");
            save.show();
            if (check.prop("checked")) {
                input.show();
            } else {
                input.hide();
            }
        });
        suffix.change(function () {
            $("#onboarding_example").html("Schema example: john.doe@" + suffix.val())
            save.val("Save");
            save.show();
        })
    }

    function getServer() {
        $.get(OC.generateUrl("/apps/secsignid/server/")).success(function (data) {
            $("#ssid_server").val(data.server);
            $("#ssid_server_port").val(data.serverport);
            $("#ssid_fallback").val(data.fallback);
            $("#ssid_fallback_port").val(data.fallbackport);
        });
        $.get(OC.generateUrl("/apps/secsignid/server/mobile/")).success(function (data) {
            $("#ssid_server_mobile").val(data);
        });
        $(".server_input").change(function () {
            $("#save_server").show();
        })
        $("#ssid_server_mobile").change(function () {
            $("#save_server_mobile").show();
        })
    }

    function addOnClicks() {
        $("#btn_management").click(function () {
            openTab($("#btn_management"), "user_management");
        });
        $("#btn_permissions").click(function () {
            openTab($("#btn_permissions"), "user_permissions");
        });
        $("#btn_settings").click(function () {
            openTab($("#btn_settings"), "secsign_settings");
        });
        $("#btn_onboarding").click(function () {
            openTab($("#btn_onboarding"), "user_onboarding");
        });
        $("#save_server").click(function () {
            save_server();
        });
        $("#save_server_mobile").click(function () {
            save_server_mobile();
        });
        $("#save_allow_enable").click(function () {
            save_allow_enable();
        });
        $("#save_onboarding").click(function () {
            save_onboarding();
        });
        $("#two_factor_auth_link").prop('href', OC.generateUrl('/settings/admin/security'));
        $('#sec_select_group').on('change', function () {
            getGroupList(this.value);
        })
    }

    getServer();
    getOnboarding();
    addOnClicks();
    getUsers();
    getPermissions();

})(OC, window, jQuery);