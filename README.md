## Circle Slider Widget for Elementor

The **Circle Slider Widget** is a custom widget for Elementor, designed to display posts in an attractive circular slider format. It offers multiple customization options, allowing you to configure:

- **Category and Post Type Selection:** Display posts from specific categories or custom post types.
- **Autoplay and Transition Speeds:** Control the timing of slide transitions and autoplay behavior.
- **Slides Display:** Set the number of slides to show and scroll at once.
- **Image Shape Options:** Choose between circular or rectangular images.
- **Story Count:** Limit the number of stories displayed per slide or show them all.
- **Post Order and Excerpt Display:** Control post sorting and toggle the excerpt visibility.

For **RTL (Right-to-Left)** language support, make sure to set the `rtl` property to `true` in the `slick()` initialization located in `/assets/js/script.js`:

```javascript
$slider.slick({
    rtl: true, // Set this to true for RTL support
    ...
});
