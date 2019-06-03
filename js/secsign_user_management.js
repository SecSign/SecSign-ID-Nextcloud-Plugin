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
    let filtered = [];
    let currentGroup = 'All groups';
    let currentFilter = '';
    let UserList = class {
        constructor(users) {
            this.users = users;
            this.filteredUsers = users;
            this.changedUsers = [];
            this.groups = initGroups(users);
            this.sortProperty = '';
        }

        get users() {
            return this.filteredUsers;
        }

        sortUsers(sorter) {
            this.sortProperty = sorter
            this.users.sort(sortBy(sorter));
        }


    }

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
                users = data;
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
        let changes = $('#save_changes');
        let length = changedUsers.length;
        if (length === 0) {
            changes.hide();
        } else {
            changes.show();
            let val_string = changes.html().replace(/[0-9]/g, length);
            if (length === 1) {
                val_string = val_string.replace('changes', 'change');
            } else {
                if (length === 2 && !val_string.includes('s'))
                    val_string += 's';
            }
            changes.html(val_string);
        }
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
            }
            showChanges();
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
        let html = '';
        data.forEach(user => {
            if (!user.displayname) {
                user.displayname = user.uid;
            }
            html += addUserRow(user);
        });
        $("#tbody").html(html);
        $("#enforced_warning").hide();
        users.forEach(user => {
            let row = $("#" + user.uid);
            if (user.enforced === "1") {
                row.find(".checkbox").prop("disabled", true);
                row.find(".checkbox").prop("checked", true);
                row.find("label").html("2FA enforced");
            }
            if ((user.enabled == 1 && !user.secsignid) || user.enforced === "1") {
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
        groups = [];
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
        showGroups(groups);
        return groups
    }

    function showGroups(groups) {
        var select = $('#sec_select_group');
        select.find("option").not("#sec_select_all").remove();
        var groupChecks = $('#sec_group_list');
        Object.keys(groups).forEach(group => {
            let html = `<option value="${group}">${group}</option>`
            select.append(html);
            let groupname = group.replace(' ', '_');
            html = `<li><input type="checkbox" class="group_list checkbox" id="sec_group_cb_${groupname}"><label for="sec_group_cb_${groupname}">${group}</label></li>`;
            groupChecks.append(html);
        });
        $('.group_list').on('change', function () {
            $('#save_allow_enable').show();
        })

    }

    /**
     * Filters the user list by group and search filter
     * 
     * @param {string} group 
     */
    function filterUsers(group, filter) {
        currentGroup = group;
        filtered = [];
        $('tbody tr').hide();
        if (group === 'All groups') {
            users.forEach(user => {
                if (fitsFilter(user, filter)) {
                    $('tr#' + user.uid).show();
                    filtered.push(user);
                }
            });
        } else {
            users.forEach(user => {
                if (groups[group].includes(user) && fitsFilter(user, filter)) {
                    $('tr#' + user.uid).show();
                    filtered.push(user);
                }
            });
        }
    }

    function fitsFilter(user, filter) {
        return user.uid.includes(filter) ||
            (user.displayname && user.displayname.includes(filter)) ||
            (user.secsignid && user.secsignid.includes(filter));
    }

    /**
     * Retrieves an array of all users from the server then shows the corresponding table
     */
    function getUsers() {
        $.get(OC.generateUrl('/apps/secsignid/ids/users/'),
            function (data) {
                users = [...data];
                filtered = [...users];
                initGroups(users);
                showTable(users);
                $(".secUi-main__barload").hide();
                $(".table").show();
                $(document).scrollTop(0);
                $("#save_changes").click(function () {
                    saveChanges();
                })
            });
    }

    function getPermissions() {
        $.get(OC.generateUrl('/apps/secsignid/allowEdit/'),
            function (data) {
                console.log(data)
                let check = $("#allow_user_enable");
                check.prop("disabled", false);
                check.prop("checked", data.allow);
                check.change(function () {
                    $("#save_allow_enable").show();
                    if ($(this).prop('checked')) {
                        $('#allow_user_groups').prop('disabled', false);
                        $('#sec_group_selector').show();
                    } else {
                        $('#allow_user_groups').prop('disabled', true);
                        $('#sec_group_selector').hide();
                    }
                });
                let groups = $('#allow_user_groups');
                groups.prop('disabled', !data.allow);
                groups.change(function () {
                    $("#save_allow_enable").show();
                    if ($(this).prop('checked')) {
                        $('#sec_group_selector').show();
                    } else {
                        $('#sec_group_selector').hide();
                    }
                });
                if(data.allowGroups){
                    groups.prop('checked', data.allowGroups)
                    $("#sec_group_selector").show();
                    if(data.groups){
                        data.groups.forEach(group => {
                            let name = group.replace(' ', '_');
                            $('#sec_group_cb_'+name).prop('checked', true);
                        });
                    }
                    
                }
                
            });
    }

    function save_allow_enable() {
        let check = $("#allow_user_enable");
        let allowGroups = $("#allow_user_groups");
        let save = $("#save_allow_enable");
        let data = {
            data: {
                allow: check.prop("checked"),
                allowGroups: allowGroups.prop('checked'),
                groups: getSelectedGroups()
            }
        };
        console.log(data);
        $.post(OC.generateUrl("/apps/secsignid/allowEdit/"), data,
            function () {
                save.html("Saved");
                save.fadeOut(3000);
            }).fail(function () {
            alert("An error occured while saving. Try again");
            check.prop("checked", !check.checked);
        });
    }

    function getSelectedGroups(){
        let selected = [];
        $('.group_list').each(function(index) {
            if($(this).prop('checked')){
                let group = $(this).prop('id').replace('sec_group_cb_','').replace('_', ' ');
                selected.push(group);
            }
        });
        return selected;
    }



    function openTab(evt, tabName) {
        $(".tabcontent").css("display", "none");
        $("#app-navigation li a").removeClass("selected");
        $("#" + tabName).css("display", "block");
        evt.addClass("selected");
        window.history.replaceState("string", tabName, '#' + tabName);
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
        }).fail(function (error) {
            console.log(`Error: ${error.responseJSON.message}`);
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

    function save_onboarding() {
        var data = {
            data: {
                enabled: $("#enable_onboarding").prop("checked"),
                suffix: $("#onboarding_suffix").val(),
                allowed: $("#enable_onboarding_choice").prop("checked"),
                groups: []
            }
        };
        $.post(OC.generateUrl("/apps/secsignid/onboarding/"), data).success(function () {
            $("#save_onboarding").html("Saved").fadeOut(3000);
        }).fail(function (error) {
            console.error(error.responseJSON.message);
        });
    }

    function getOnboarding() {
        let check = $("#enable_onboarding");
        let checkChoice = $("#enable_onboarding_choice");
        let input = $(".onboarding_input");
        let suffix = $("#onboarding_suffix");
        let save = $("#save_onboarding");
        $.get(OC.generateUrl("/apps/secsignid/onboarding/"))
            .success(function (data) {
                check.prop("checked", data.enabled);
                checkChoice.prop("checked", data.allowed);
                if(!data.enabled){
                    checkChoice.prop('disabled', true);
                    input.hide();
                }
                suffix.val(data.suffix);
                $("#onboarding_example").html("John.doe@" + suffix.val())
                if (data.enabled) {
                    input.show();
                }
            });
        var onchange = function () {
            save.html("Save");
            save.show();
            if (check.prop("checked")) {
                input.show();
                checkChoice.prop('disabled', false)
            } else {
                input.hide();
                checkChoice.prop('disabled', true);
            }
        }
        check.change(onchange);
        checkChoice.change(onchange);
        suffix.on('keyup', function () {
            $("#onboarding_example").html("John.doe@" + suffix.val())
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

    function refreshList(except) {
        $("th").not(except).find(".sort_indicator").removeClass("icon_ascending");
        $("th").not(except).find(".sort_indicator").removeClass("icon_descending");
        $("tr").not("#sec_header_row").remove()
        showTable(users);
        filterUsers(currentGroup, currentFilter);
    }

    var sort_property;

    function sortBy(sortBy, header, users) {
        if (sort_property == sortBy) {
            sort_property = '-' + sortBy;
            $(header).find(".sort_indicator").switchClass("icon_ascending", "icon_descending");
        } else {
            sort_property = sortBy;
            $(header).find(".sort_indicator").switchClass("icon_descending", "icon_ascending");
        }
        users = users.sort(dynamicSort(sort_property));
        filtered = filtered.sort(dynamicSort(sort_property));
        refreshList(header);
    }


    function addSorts() {
        $('#sec-th-username').on("click", function () {
            sortBy('uid', this, users);
        });
        $('#sec-th-displayname').on("click", function () {
            sortBy('displayname', this, users);
        });
        $('#sec-th-secsignid').on("click", function () {
            sortBy('secsignid', this, users);
        });
        $('#sec-th-2fa').on("click", function () {
            sortBy('enabled', this, users);
        });
    }

    function dynamicSort(property) {
        var sortOrder = 1;
        if (property[0] === "-") {
            sortOrder = -1;
            property = property.substr(1);
        }
        return function (a, b) {
            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
            return result * sortOrder;
        }
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
            filterUsers(this.value, currentFilter);
        });
        $('#sec_search_input').on('keyup', function () {
            currentFilter = $(this).val();
            filterUsers(currentGroup, currentFilter);
        })
    }

    getServer();
    getOnboarding();
    addOnClicks();
    getUsers();
    getPermissions();
    addSorts();
    let tab = window.location.hash ? window.location.hash : '#user_management';
    let btn = '#btn' + tab.substring(tab.indexOf('_'));
    console.log(tab, btn, $(btn).length);
    if ($(btn).length) {
        openTab($(btn), tab.split('#')[1]);
    } else {
        openTab($('#btn_management'), 'user_management');
    }


})(OC, window, jQuery);