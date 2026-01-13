# Take-home notes

Answer briefly:

1) Decisions you made and why:
- Implemented a single `POST /api/remix` endpoint with server-side validation (required, string, min:20, max:280) so rules are centralized and tests can assert behavior.
- Placed generation logic in `App/Services/Remix/RemixService` to keep concerns separated and make the variants deterministic and easy to unit-test.
- Kept the frontend minimal (`/tools/remix`) with client-side checks, loading/error states and copy buttons to deliver a usable UX without over-engineering.

2) Tradeoffs to keep it small:
- Deterministic templates (no external APIs) reduce variability but keep the implementation quick, reliable and offline.
- No database or auth to stay within the timebox; this removes persistence and multi-user features.
- Limited UI polish and no E2E tests due to the 4-hour constraint.

3) If you had more time, what would you improve next:
- Add a template library and heuristics for more diverse, higher-quality hooks and CTAs.
- Add E2E tests, clipboard success UI, accessibility improvements, and keyboard shortcuts for copy actions.
- Deploy to a free platform and add a simple health-check endpoint and basic monitoring.
