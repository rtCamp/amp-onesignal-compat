# AMP Skeleton Compatability

The skeleton plugin to add AMP compatibility to your theme.

## Notes

- Rename plugin's folder to amp-skeleton-compat when you use this skeleton.
- Replace Namespace AMP_Plugin_Name_Compat by your namespace in every file. 
- Change Plugin Name
- Add your name as author
- Add your plugin URI

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