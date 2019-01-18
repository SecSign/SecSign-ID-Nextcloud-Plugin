(function (OC, window, $) {
    'use strict';

    function save(userid, id) {
        $.post(OC.generateUrl('/apps/secsignid/id/enable/'), {
                secsignid: id,
                uid: userid
            },
            function (data) {
                console.log(data);
            }
        ).fail(function () {
            console.log("failed to save");
        });
    }

    function getUsers() {
        $.get(OC.generateUrl('/apps/secsignid/ids/users/'),
            function (data) {
                let html = '';
                data.forEach(user => {
                    let displayname = user.displayname == null ? user.uid : user.displayname;
                    let secsignid = user.secsignid == null ? "-" : user.secsignid;
                    html += "<tr id='" + user.uid + "'>";
                    html += "   <td>" + user.uid + "</td>";
                    html += "   <td>" + displayname + "</td>";
                    if (secsignid !== "-") {
                        html += "   <td class='center'><input type='text' value='" + secsignid + "'></td>";
                    }else{
                        html += "   <td class='center'><input type='text' placeholder='None'></td>";
                    }
                    if (user.enabled == 1) {
                        html += "<td class='center'><input type='checkbox' name='enabled_" + user.uid + "' checked></td>";
                    } else {
                        html += "<td class='center'><input type='checkbox' name='enabled_" + user.uid + "'></td>";
                    }
                    html += "</tr>";
                });
                $("#table").append(html);
                $(".lds-roller").hide();
                $("#table").show();
            });
    }

    getUsers();

})(OC, window, jQuery);