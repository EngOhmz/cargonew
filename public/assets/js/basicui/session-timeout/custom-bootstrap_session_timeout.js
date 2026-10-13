var SessionTimeout=function() {
    'use-strict';
    var e=function() {
        $.sessionTimeout( {
            title:"Session Timeout Notification",
            message:"Your session is about to expire.",
            keepAliveUrl:"",
            redirUrl:"/authentications/style1/locked",
            logoutUrl:"/authentications/style1/login",
            warnAfter:1000,
            redirAfter:15000,
            ignoreUserActivity:!0,
            countdownMessage:"Redirecting in {timer}.",
            countdownBar: !0
        }
        )
    };
    return {
        init:function() {
            e()
        }
    }
}
();
jQuery(document).ready(function() {
    SessionTimeout.init()
}
);
