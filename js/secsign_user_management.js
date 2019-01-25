/**
 * This script is responsible for the logic behind the user management screen.
 */
(function (OC, window, $) {
    'use strict';
    let users;
    let changedUsers = [];

    /**
     * Returns an array of objects holding the values for each changed User
     */
    function getChanges() {
        let updates = [];
        changedUsers.forEach(uid => {
            let row = $("#" + uid);
            let secsignid = row.find(".ssid input").val();
            let enabled = row.find("#enabled input").is(':checked') ? 1 : 0;
            if (secsignid == '') {
                secsignid = null;
                enabled = 0;
            }
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
            console.log("an error occurred while saving");
        });
    }

    /**
     * Returns the html string for a table row for the given user
     * @param {Object} user the user a row will be created for
     */
    function addUserRow(user) {
        let html = '';
        let displayname = user.displayname == null ? user.uid : user.displayname;
        let secsignid = user.secsignid == null ? "-" : user.secsignid;
        html += "<tr id='" + user.uid + "'>";
        html += "   <td>" + user.uid + "</td>";
        html += "   <td class='displayname'>" + displayname + "</td>";
        if (secsignid !== "-") {
            html += "   <td class='center ssid'><input type='text' value='" + secsignid + "'></td>";
            if (user.enabled == 1) {
                html += "<td id='enabled' class='center'><input type='checkbox' checked></td>";
            } else {
                html += "<td id='enabled' class='center'><input type='checkbox'></td>";
            }
        } else {
            html += "<td class='center ssid'><input type='text' placeholder='None'></td>";
            html += "<td id='enabled' class='center'><input disabled type='checkbox'></td>";
        }
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
            if (user.secsignid == null) {
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
        data.forEach(user => {
            html += addUserRow(user);
        });
        $("#tbody").html(html);
        users.forEach(user => {
            $("#" + user.uid).find(".ssid input").change(function () {
                changedID($(this).val(), user);
            });
            $("#" + user.uid).find("#enabled").change(function () {
                changedEnabled($('input', this).is(":checked"), user);
            });
        });
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

    getUsers();

})(OC, window, jQuery);