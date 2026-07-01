# Google Play Store Listing — BYOB Map

## App title (max 30 characters)
BYOB Map

(28 chars if you want the longer form instead: "BYOB Map: Philly BYOB Finder")

## Short description (max 80 characters)
Find Philly restaurants where you can BYOB. See fees, then navigate instantly.

(77 characters)

## Full description (max 4000 characters)

BYOB Map helps you find Philadelphia restaurants that let you bring your own bottle — fast.

No more digging through review sites or restaurant websites trying to figure out if a place allows outside wine, or what they'll charge you to open it. BYOB Map shows you the corkage policy right on the restaurant card, before you tap in.

WHAT YOU CAN DO
• Browse Philadelphia's BYOB restaurants in one list, no login required
• See the corkage fee policy at a glance — Free BYOB, Corkage Fee, or Ask Us
• Filter by cuisine: Italian, Japanese, Mediterranean, Seafood, Sushi, Pizza, Asian, Mexican, Thai, Ramen, French, and more
• Search restaurants by name
• Tap a restaurant for address, phone number, and full corkage details
• Tap once to call, tap once to get directions in Google Maps
• Switch to map view to see every BYOB spot near you

WHY BYOB MAP
Corkage fees are usually buried in a review thread or nowhere at all. BYOB Map puts that information front and center, so you can decide where to eat without guessing. Whether you're a longtime Philadelphian or visiting for the first time, you can go from opening the app to walking out the door with a bottle in under 30 seconds.

No accounts. No reservations. No reviews to wade through. Just restaurants, corkage policies, and directions.

## Category
Food & Drink

## Content rating notes
References to alcohol (corkage / BYOB policy information only — app does not sell, serve, or facilitate alcohol purchase). Select "References to alcohol" if prompted during Play Console content rating questionnaire.

## Suggested keywords (for internal reference, not pasted into any single field)
BYOB, corkage fee, Philadelphia restaurants, bring your own wine, Philly dining, wine friendly restaurants

## Privacy Policy URL
**Live: https://byobmap.com/byob-map/privacy** — paste this into the Play Console "Privacy Policy" field.

Site structure note: `byobmap.com` root is a placeholder page ("App pages coming soon"), reserved for a future multi-app landing page. Each app's privacy policy lives at `byobmap.com/<app-slug>/privacy` — this app's slug is `byob-map`.

⚠️ The live page still shows "Small Tools Studio" as the developer/entity name (inferred from the package name, unconfirmed). Fix this in `public/byob-map/privacy/index.html` and redeploy (`firebase deploy --only hosting`) before submitting to Play Console if that name is wrong — it's publicly visible now.

## Still needed before submission
- Screenshots (phone: at least 2, recommended 4–8) — Dark Mode fix (Contract 20) is done, can pull from FlutterFlow preview or APK now
- Feature graphic (1024 × 500 PNG)
- App icon (512 × 512 PNG, Play Store listing size — separate from the 1024×1024 in-app icon)
- **Play Console "Data safety" form** — separate from the privacy policy text, this is a required questionnaire in Play Console. Since Firebase Google Analytics is confirmed **Enabled** (checked 2026-07-01 via Firebase Console → Integrations), you must declare: App activity (app interactions), Device or other IDs, and Approximate location as collected, purpose "Analytics", not shared with third parties. Precise location (GPS) should be declared separately as collected for "App functionality," not shared, not stored on servers. Getting this form inconsistent with the actual privacy policy is a common cause of Play Store rejection.
