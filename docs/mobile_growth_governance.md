# Mobile Growth Governance

## Data policy
- `mobile_analytics_events` keeps growth telemetry for 120 days.
- Personal export endpoint: `GET /api/analytics/my-events/export` (auth required).
- Personal delete endpoint: `DELETE /api/analytics/my-events` (auth required).

## Security controls
- Optional ingestion header: `X-Analytics-Key` matched by `MOBILE_ANALYTICS_KEY`.
- Source whitelist: `mobile`, `web`, `api`.
- Properties are sanitized (scalar values, bounded key/value lengths).

## Release governance
- Feature/campaign release uses admin `Growth Dashboard` and `Push Campaigns`.
- Rollback trigger examples:
  - trial request rate drops by >15% week-over-week
  - booking start rate drops by >10% week-over-week

## Ops checklist
- Run migrations before rollout.
- Set `MOBILE_ANALYTICS_KEY` in production env.
- Review retention cleanup in weekly DB maintenance.
