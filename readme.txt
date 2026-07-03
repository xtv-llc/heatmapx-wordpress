=== HeatMapX – Heatmaps & A/B Testing ===
Contributors: tcmcya
Tags: heatmap, ab testing, analytics, click tracking, conversion
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Heatmaps, scroll maps, session analytics and A/B testing. Paste your site key — no theme editing required.

== Description ==

[HeatMapX](https://heatmapx.com) shows where visitors click, how far they scroll, and which A/B variant converts better — with an AI-agent-friendly CLI (Claude Code / Codex) on top.

This plugin adds the HeatMapX tracking snippet to your site automatically:

* **One-minute setup** — paste your site key under Settings → HeatMapX. No theme editing.
* **Clean data** — optionally skip logged-in administrators so your own clicks never pollute the heatmap.
* **A/B testing ready** — experiments created in the HeatMapX dashboard run through the same snippet.
* **Free plan available** — create an account at heatmapx.com and start free.

== External services ==

This plugin loads the HeatMapX tracking script from `heatmapx.com` and sends anonymized interaction events (clicks, scroll depth, mouse movement, page URL, viewport size) from your visitors' browsers to HeatMapX so that heatmaps and analytics can be rendered in your dashboard.

* Service: HeatMapX (operated by XTV LLC)
* Privacy policy: https://heatmapx.com/en/privacy
* Terms of service: https://heatmapx.com/en/terms

Data is only sent after you save a site key. Deactivating the plugin stops all data collection immediately.

== Installation ==

1. Install and activate the plugin.
2. Create a free account at [heatmapx.com](https://heatmapx.com) and copy your site key.
3. Go to **Settings → HeatMapX**, paste the key, save.
4. Open your site in another browser — data appears in your HeatMapX dashboard within a minute.

== Frequently Asked Questions ==

= Do I need a HeatMapX account? =

Yes. A free plan is available at heatmapx.com.

= Does it slow down my site? =

The tracker is a small script loaded asynchronously (`async`), so it does not block page rendering.

= Why don't I see my own visits? =

By default, logged-in administrators are not tracked. You can change this under Settings → HeatMapX.

== Screenshots ==

1. Settings page — paste your site key and save.
2. HeatMapX dashboard — click heatmap rendered from collected data.

== Changelog ==

= 1.0.0 =
* Initial release: automatic snippet insertion, admin exclusion option, Japanese translation.
