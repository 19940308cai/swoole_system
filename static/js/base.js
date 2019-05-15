const CUSTOMER_UID = "customer_uid";
const PROVIDER_UID = "provider_uid";
const STORE_PROVIDER_UID = "store_provider_uid";

const PING = "ping";
const PONG = "pong";

function getUidKey(user_type) {
    switch (user_type) {
        case "customer":
            return CUSTOMER_UID;
            break;
        case "provider":
            return PROVIDER_UID;
            break;
        case "store":
            return STORE_PROVIDER_UID;
            break;
    }
}

window.ip = null;
window.ws = null;

function getNode(user_type, uid) {
    return new Promise((resolve) => {
        $.ajax({
            url: "http://www.node_manager.com:8823/getNode",
            type: "post",
            async: false,
            data: {user_type: user_type, uid: uid},
            success: function (data) {
                if (data.message == "success") {
                    if (data.data.node_ip) {
                        window.ip = data.data.node_ip;
                        window.ws = new WebSocket("ws://" + window.ip + "?user_type=" + user_type + "&uid=" + uid);
                        resolve();
                        setInterval(function () {
                            window.ws.send(PING);
                        }, 2500);
                    }
                }
            }
        })
    })
}

