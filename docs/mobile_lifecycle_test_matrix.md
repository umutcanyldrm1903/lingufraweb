# Mobile Lifecycle Edge-Case Matrix

## Notification deep-link cases
- App killed + tap notification -> route should open target screen.
- App background + tap notification -> foreground and navigate once.
- App foreground + receive + tap from tray -> navigate without duplicate.

## Network and retry cases
- Offline while speaking funnel events are logged -> sync when online.
- Slow network (2G simulation) -> no app freeze on analytics sync.
- Backend 401/429 responses -> events kept locally, retry later.

## User state cases
- Logged-out user receives `/student` route payload -> fallback to `/login`.
- Logged-in instructor receives student route -> fallback to instructor shell.
- Auth token expires before deep-link navigation -> redirect to login.
