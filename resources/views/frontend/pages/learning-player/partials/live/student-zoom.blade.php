<!DOCTYPE html>

<head>
    <title>{{ $liveLesson->title . ' | ' . $setting?->app_name }}</title>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting?->favicon) }}">
    <style type="text/css">
        .ax-outline-blue-important:first-child {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Dependencies for client view and component view -->
    <script src="https://source.zoom.us/3.8.5/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.8.5/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.8.5/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.8.5/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.8.5/lib/vendor/lodash.min.js"></script>

    <!-- For Client View -->
    <script src="https://source.zoom.us/zoom-meeting-3.8.5.min.js"></script>
    <script>
        "use strict";
        // Zoom Meeting SDK expects digits-only meeting number; keep it consistent with server signature.
        var mn = String(@json((string) $liveLesson->meeting_id)).replace(/[^0-9]/g, "");
        var user_name = @json((string) userAuth()->name);
        var pwd = @json((string) ($liveLesson->password ?? ''));
        var role = @json((int) $role); // 1 is host, 0 is attendee
        var email = @json((string) userAuth()->email);
        var lang = "en-US";
        var sdkKey = @json((string) $sdkKey); // SDK Key / Client ID
        var signature = @json((string) $signature); // generated on server
        var leaveUrl = @json(userAuth()->role === 'instructor' ? route('instructor.lessons.index') : route('student.enrolled-courses'));

        // Initialize and join (signature generated on server)
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();
        ZoomMtg.i18n.load(lang);

        ZoomMtg.init({
            leaveUrl: leaveUrl,
            disableCORP: !window.crossOriginIsolated, // default true
            success: function() {
                ZoomMtg.join({
                    meetingNumber: mn,
                    userName: user_name,
                    signature: signature,
                    sdkKey: sdkKey,
                    userEmail: email,
                    passWord: pwd,
                    success: function(res) {
                        ZoomMtg.getAttendeeslist({});
                        ZoomMtg.getCurrentUser({
                            success: function(res) {},
                        });
                    },
                    error: function(res) {
                        console.error('Zoom join error', res);
                        if (res && res.errorCode === 3000 && role === 1) {
                            // Host can't start while already hosting another meeting on the same Zoom account.
                            // Offer a safe fallback: reload as attendee.
                            try {
                                var url = new URL(window.location.href);
                                if (!url.searchParams.has('as_attendee')) {
                                    if (confirm('Bu Zoom hesabi ile zaten baska bir toplantiyi host ediyorsunuz.\n\nDiger toplantiyi bitirip tekrar deneyin.\n\nIsterseniz simdi "Katilimci" olarak giris yapabiliriz (host kontrolu olmaz).')) {
                                        url.searchParams.set('as_attendee', '1');
                                        window.location.replace(url.toString());
                                    }
                                }
                            } catch (e) {}
                        }
                    },
                });
            },
            error: function(res) {
                console.error('Zoom init error', res);
            },
        });

        ZoomMtg.inMeetingServiceListener("onUserJoin", function(data) {});
        ZoomMtg.inMeetingServiceListener("onUserLeave", function(data) {});
        ZoomMtg.inMeetingServiceListener("onUserIsInWaitingRoom", function(data) {});
        ZoomMtg.inMeetingServiceListener("onMeetingStatus", function(data) {});

        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable specific key combinations (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U)
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) || // Ctrl+Shift+I, Ctrl+Shift+J
                (e.ctrlKey && e.key === 'U')) { // Ctrl+U
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>

</html>
