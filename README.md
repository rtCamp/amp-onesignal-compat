# AMP One Signal Push Compatibility

Plugin to add AMP compatibility to OneSignal – Web Push Notifications plugin.

## Notes

- You need to install OneSignal – Web Push Notifications
- Do OneSignal setup
- Add API id and key
- must be on https
- Subdomains are not supported at movements.

## Plugin Structure

```markdown
.
├── css
│   ├── amp-style.css
├── sanitizers
│   ├── class-sanitizer.php
└── amp-skeleton-compat.php
```
## Sanitizers

The plugin uses `amp_content_sanitizers` filter to add custom sanitizers, we have added a two examples which add simple toggle for menu and search using amp-state and amp-bind.

## Custom CSS
You can add your custom CSS or override the CSS in in `css/amp-style.css` make sure you don't exceed overall budget of 75KB

### Need a feature in plugin?
Feel free to create a issue and will add more examples.