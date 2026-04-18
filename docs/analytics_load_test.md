# Analytics Endpoint Load Test

## Goal
Validate `/api/analytics/events` and `/api/analytics/store-metrics` under burst traffic.

## Suggested command (k6)
```bash
k6 run analytics_events_load.js
```

## Minimal acceptance criteria
- p95 latency < 500ms for `/api/analytics/events`
- error rate < 1%
- DB write queue remains healthy (no lock escalation)

## Notes
- Use `MOBILE_ANALYTICS_KEY` in test headers when key is enabled.
- Test with realistic payload size (`events` length 20..100).
