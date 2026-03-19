<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_user_logged_in() ) {
    wp_safe_redirect( admin_url() );
    exit;
}


$current_lang_name = 'Français';
$current_lang_slug = 'fr';
$current_lang_flag = '🇫🇷';
$pll_languages      = array();

if ( function_exists( 'pll_the_languages' ) ) {
    $pll_languages = pll_the_languages(
        array(
            'raw'                    => 1,
            'hide_if_empty'          => 0,
            'hide_if_no_translation' => 0,
        )
    );

    if ( ! empty( $pll_languages ) ) {
        foreach ( $pll_languages as $lang ) {
            if ( ! empty( $lang['current_lang'] ) ) {
                $current_lang_name = $lang['name'];
                $current_lang_slug = $lang['slug'];
                $current_lang_flag = ! empty( $lang['flag'] ) ? wp_strip_all_tags( $lang['flag'] ) : strtoupper( $lang['slug'] );
                break;
            }
        }
    }
}

$owner_access_url = home_url( '/' );
$support_url      = 'https://logestay.com/';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        *, ::before, ::after {
  --tw-border-spacing-x: 0;
  --tw-border-spacing-y: 0;
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position:  ;
  --tw-gradient-via-position:  ;
  --tw-gradient-to-position:  ;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
  --tw-contain-size:  ;
  --tw-contain-layout:  ;
  --tw-contain-paint:  ;
  --tw-contain-style:  ;
}

::backdrop {
  --tw-border-spacing-x: 0;
  --tw-border-spacing-y: 0;
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position:  ;
  --tw-gradient-via-position:  ;
  --tw-gradient-to-position:  ;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
  --tw-contain-size:  ;
  --tw-contain-layout:  ;
  --tw-contain-paint:  ;
  --tw-contain-style:  ;
}/*
! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com
*//*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/

*,
::before,
::after {
  box-sizing: border-box; /* 1 */
  border-width: 0; /* 2 */
  border-style: solid; /* 2 */
  border-color: #e5e7eb; /* 2 */
}

::before,
::after {
  --tw-content: '';
}

/*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
5. Use the user's configured `sans` font-feature-settings by default.
6. Use the user's configured `sans` font-variation-settings by default.
7. Disable tap highlights on iOS
*/

html,
:host {
  line-height: 1.5; /* 1 */
  -webkit-text-size-adjust: 100%; /* 2 */
  -moz-tab-size: 4; /* 3 */
  -o-tab-size: 4;
     tab-size: 4; /* 3 */
  font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"; /* 4 */
  font-feature-settings: normal; /* 5 */
  font-variation-settings: normal; /* 6 */
  -webkit-tap-highlight-color: transparent; /* 7 */
}

/*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/

body {
  margin: 0; /* 1 */
  line-height: inherit; /* 2 */
}

/*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

hr {
  height: 0; /* 1 */
  color: inherit; /* 2 */
  border-top-width: 1px; /* 3 */
}

/*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

abbr:where([title]) {
  -webkit-text-decoration: underline dotted;
          text-decoration: underline dotted;
}

/*
Remove the default font size and weight for headings.
*/

h1,
h2,
h3,
h4,
h5,
h6 {
  font-size: inherit;
  font-weight: inherit;
}

/*
Reset links to optimize for opt-in styling instead of opt-out.
*/

a {
  color: inherit;
  text-decoration: inherit;
}

/*
Add the correct font weight in Edge and Safari.
*/

b,
strong {
  font-weight: bolder;
}

/*
1. Use the user's configured `mono` font-family by default.
2. Use the user's configured `mono` font-feature-settings by default.
3. Use the user's configured `mono` font-variation-settings by default.
4. Correct the odd `em` font sizing in all browsers.
*/

code,
kbd,
samp,
pre {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; /* 1 */
  font-feature-settings: normal; /* 2 */
  font-variation-settings: normal; /* 3 */
  font-size: 1em; /* 4 */
}

/*
Add the correct font size in all browsers.
*/

small {
  font-size: 80%;
}

/*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/

sub,
sup {
  font-size: 75%;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
}

sub {
  bottom: -0.25em;
}

sup {
  top: -0.5em;
}

/*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/

table {
  text-indent: 0; /* 1 */
  border-color: inherit; /* 2 */
  border-collapse: collapse; /* 3 */
}

/*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

button,
input,
optgroup,
select,
textarea {
  font-family: inherit; /* 1 */
  font-feature-settings: inherit; /* 1 */
  font-variation-settings: inherit; /* 1 */
  font-size: 100%; /* 1 */
  font-weight: inherit; /* 1 */
  line-height: inherit; /* 1 */
  letter-spacing: inherit; /* 1 */
  color: inherit; /* 1 */
  margin: 0; /* 2 */
  padding: 0; /* 3 */
}

/*
Remove the inheritance of text transform in Edge and Firefox.
*/

button,
select {
  text-transform: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

button,
input:where([type='button']),
input:where([type='reset']),
input:where([type='submit']) {
  -webkit-appearance: button; /* 1 */
  background-color: transparent; /* 2 */
  background-image: none; /* 2 */
}

/*
Use the modern Firefox focus style for all focusable elements.
*/

:-moz-focusring {
  outline: auto;
}

/*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

:-moz-ui-invalid {
  box-shadow: none;
}

/*
Add the correct vertical alignment in Chrome and Firefox.
*/

progress {
  vertical-align: baseline;
}

/*
Correct the cursor style of increment and decrement buttons in Safari.
*/

::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
  height: auto;
}

/*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

[type='search'] {
  -webkit-appearance: textfield; /* 1 */
  outline-offset: -2px; /* 2 */
}

/*
Remove the inner padding in Chrome and Safari on macOS.
*/

::-webkit-search-decoration {
  -webkit-appearance: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

::-webkit-file-upload-button {
  -webkit-appearance: button; /* 1 */
  font: inherit; /* 2 */
}

/*
Add the correct display in Chrome and Safari.
*/

summary {
  display: list-item;
}

/*
Removes the default spacing and border for appropriate elements.
*/

blockquote,
dl,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
hr,
figure,
p,
pre {
  margin: 0;
}

fieldset {
  margin: 0;
  padding: 0;
}

legend {
  padding: 0;
}

ol,
ul,
menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

/*
Reset default styling for dialogs.
*/
dialog {
  padding: 0;
}

/*
Prevent resizing textareas horizontally by default.
*/

textarea {
  resize: vertical;
}

/*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

input::-moz-placeholder, textarea::-moz-placeholder {
  opacity: 1; /* 1 */
  color: #9ca3af; /* 2 */
}

input::placeholder,
textarea::placeholder {
  opacity: 1; /* 1 */
  color: #9ca3af; /* 2 */
}

/*
Set the default cursor for buttons.
*/

button,
[role="button"] {
  cursor: pointer;
}

/*
Make sure disabled buttons don't get the pointer cursor.
*/
:disabled {
  cursor: default;
}

/*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
   This can trigger a poorly considered lint error in some tools but is included by design.
*/

img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block; /* 1 */
  vertical-align: middle; /* 2 */
}

/*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

img,
video {
  max-width: 100%;
  height: auto;
}

/* Make elements with the HTML hidden attribute stay hidden by default */
[hidden]:where(:not([hidden="until-found"])) {
  display: none;
}
.fixed {
  position: fixed;
}
.absolute {
  position: absolute;
}
.relative {
  position: relative;
}
.inset-0 {
  inset: 0px;
}
.right-0 {
  right: 0px;
}
.right-3 {
  right: 0.75rem;
}
.right-4 {
  right: 1rem;
}
.top-1\/2 {
  top: 50%;
}
.top-4 {
  top: 1rem;
}
.z-10 {
  z-index: 10;
}
.z-20 {
  z-index: 20;
}
.z-50 {
  z-index: 50;
}
.mb-2 {
  margin-bottom: 0.5rem;
}
.mb-3 {
  margin-bottom: 0.75rem;
}
.mb-4 {
  margin-bottom: 1rem;
}
.mb-6 {
  margin-bottom: 1.5rem;
}
.mb-8 {
  margin-bottom: 2rem;
}
.mt-0\.5 {
  margin-top: 0.125rem;
}
.mt-2 {
  margin-top: 0.5rem;
}
.mt-6 {
  margin-top: 1.5rem;
}
.mt-8 {
  margin-top: 2rem;
}
.block {
  display: block;
}
.flex {
  display: flex;
}
.inline-flex {
  display: inline-flex;
}
.hidden {
  display: none;
}
.h-10 {
  height: 2.5rem;
}
.h-16 {
  height: 4rem;
}
.h-4 {
  height: 1rem;
}
.h-5 {
  height: 1.25rem;
}
.h-8 {
  height: 2rem;
}
.min-h-screen {
  min-height: 100vh;
}
.w-10 {
  width: 2.5rem;
}
.w-16 {
  width: 4rem;
}
.w-4 {
  width: 1rem;
}
.w-48 {
  width: 12rem;
}
.w-5 {
  width: 1.25rem;
}
.w-8 {
  width: 2rem;
}
.w-full {
  width: 100%;
}
.max-w-md {
  max-width: 28rem;
}
.flex-1 {
  flex: 1 1 0%;
}
.flex-shrink-0 {
  flex-shrink: 0;
}
.-translate-y-1\/2 {
  --tw-translate-y: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}
.rotate-180 {
  --tw-rotate: 180deg;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}
.transform {
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}
@keyframes spin {

  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
.cursor-pointer {
  cursor: pointer;
}
.flex-col {
  flex-direction: column;
}
.items-start {
  align-items: flex-start;
}
.items-center {
  align-items: center;
}
.justify-center {
  justify-content: center;
}
.justify-between {
  justify-content: space-between;
}
.gap-1 {
  gap: 0.25rem;
}
.gap-2 {
  gap: 0.5rem;
}
.gap-3 {
  gap: 0.75rem;
}
.space-y-4 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-y-reverse: 0;
  margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse)));
  margin-bottom: calc(1rem * var(--tw-space-y-reverse));
}
.space-y-5 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-y-reverse: 0;
  margin-top: calc(1.25rem * calc(1 - var(--tw-space-y-reverse)));
  margin-bottom: calc(1.25rem * var(--tw-space-y-reverse));
}
.space-y-6 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-y-reverse: 0;
  margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
  margin-bottom: calc(1.5rem * var(--tw-space-y-reverse));
}
.overflow-hidden {
  overflow: hidden;
}
.rounded {
  border-radius: 0.25rem;
}
.rounded-2xl {
  border-radius: 1rem;
}
.rounded-full {
  border-radius: 9999px;
}
.rounded-lg {
  border-radius: 0.5rem;
}
.rounded-xl {
  border-radius: 0.75rem;
}
.border {
  border-width: 1px;
}
.border-t {
  border-top-width: 1px;
}
.border-gray-100 {
  --tw-border-opacity: 1;
  border-color: rgb(243 244 246 / var(--tw-border-opacity, 1));
}
.border-gray-200 {
  --tw-border-opacity: 1;
  border-color: rgb(229 231 235 / var(--tw-border-opacity, 1));
}
.border-gray-300 {
  --tw-border-opacity: 1;
  border-color: rgb(209 213 219 / var(--tw-border-opacity, 1));
}
.border-green-200 {
  --tw-border-opacity: 1;
  border-color: rgb(187 247 208 / var(--tw-border-opacity, 1));
}
.border-red-200 {
  --tw-border-opacity: 1;
  border-color: rgb(254 202 202 / var(--tw-border-opacity, 1));
}
.bg-black\/40 {
  background-color: rgb(0 0 0 / 0.4);
}
.bg-gray-100 {
  --tw-bg-opacity: 1;
  background-color: rgb(243 244 246 / var(--tw-bg-opacity, 1));
}
.bg-gray-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(249 250 251 / var(--tw-bg-opacity, 1));
}
.bg-green-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(240 253 244 / var(--tw-bg-opacity, 1));
}
.bg-orange-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(255 247 237 / var(--tw-bg-opacity, 1));
}
.bg-red-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(254 242 242 / var(--tw-bg-opacity, 1));
}
.bg-white {
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
}
.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
.from-gray-50 {
  --tw-gradient-from: #f9fafb var(--tw-gradient-from-position);
  --tw-gradient-to: rgb(249 250 251 / 0) var(--tw-gradient-to-position);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.from-orange-100 {
  --tw-gradient-from: #ffedd5 var(--tw-gradient-from-position);
  --tw-gradient-to: rgb(255 237 213 / 0) var(--tw-gradient-to-position);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.from-orange-500 {
  --tw-gradient-from: #f97316 var(--tw-gradient-from-position);
  --tw-gradient-to: rgb(249 115 22 / 0) var(--tw-gradient-to-position);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.to-gray-100 {
  --tw-gradient-to: #f3f4f6 var(--tw-gradient-to-position);
}
.to-orange-50 {
  --tw-gradient-to: #fff7ed var(--tw-gradient-to-position);
}
.to-orange-600 {
  --tw-gradient-to: #ea580c var(--tw-gradient-to-position);
}
.p-2 {
  padding: 0.5rem;
}
.p-4 {
  padding: 1rem;
}
.p-6 {
  padding: 1.5rem;
}
.p-8 {
  padding: 2rem;
}
.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}
.px-6 {
  padding-left: 1.5rem;
  padding-right: 1.5rem;
}
.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}
.py-2\.5 {
  padding-top: 0.625rem;
  padding-bottom: 0.625rem;
}
.py-3 {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
}
.py-3\.5 {
  padding-top: 0.875rem;
  padding-bottom: 0.875rem;
}
.py-4 {
  padding-top: 1rem;
  padding-bottom: 1rem;
}
.py-8 {
  padding-top: 2rem;
  padding-bottom: 2rem;
}
.pr-12 {
  padding-right: 3rem;
}
.pt-2 {
  padding-top: 0.5rem;
}
.pt-6 {
  padding-top: 1.5rem;
}
.text-center {
  text-align: center;
}
.text-2xl {
  font-size: 1.5rem;
  line-height: 2rem;
}
.text-lg {
  font-size: 1.125rem;
  line-height: 1.75rem;
}
.text-sm {
  font-size: 0.875rem;
  line-height: 1.25rem;
}
.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}
.text-xs {
  font-size: 0.75rem;
  line-height: 1rem;
}
.font-bold {
  font-weight: 700;
}
.font-medium {
  font-weight: 500;
}
.font-semibold {
  font-weight: 600;
}
.text-gray-400 {
  --tw-text-opacity: 1;
  color: rgb(156 163 175 / var(--tw-text-opacity, 1));
}
.text-gray-500 {
  --tw-text-opacity: 1;
  color: rgb(107 114 128 / var(--tw-text-opacity, 1));
}
.text-gray-600 {
  --tw-text-opacity: 1;
  color: rgb(75 85 99 / var(--tw-text-opacity, 1));
}
.text-gray-700 {
  --tw-text-opacity: 1;
  color: rgb(55 65 81 / var(--tw-text-opacity, 1));
}
.text-gray-800 {
  --tw-text-opacity: 1;
  color: rgb(31 41 55 / var(--tw-text-opacity, 1));
}
.text-green-600 {
  --tw-text-opacity: 1;
  color: rgb(22 163 74 / var(--tw-text-opacity, 1));
}
.text-green-700 {
  --tw-text-opacity: 1;
  color: rgb(21 128 61 / var(--tw-text-opacity, 1));
}
.text-orange-500 {
  --tw-text-opacity: 1;
  color: rgb(249 115 22 / var(--tw-text-opacity, 1));
}
.text-red-600 {
  --tw-text-opacity: 1;
  color: rgb(220 38 38 / var(--tw-text-opacity, 1));
}
.text-red-700 {
  --tw-text-opacity: 1;
  color: rgb(185 28 28 / var(--tw-text-opacity, 1));
}
.text-white {
  --tw-text-opacity: 1;
  color: rgb(255 255 255 / var(--tw-text-opacity, 1));
}
.placeholder-gray-400::-moz-placeholder {
  --tw-placeholder-opacity: 1;
  color: rgb(156 163 175 / var(--tw-placeholder-opacity, 1));
}
.placeholder-gray-400::placeholder {
  --tw-placeholder-opacity: 1;
  color: rgb(156 163 175 / var(--tw-placeholder-opacity, 1));
}
.shadow-2xl {
  --tw-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
  --tw-shadow-colored: 0 25px 50px -12px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.shadow-lg {
  --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.shadow-md {
  --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.shadow-sm {
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.shadow-xl {
  --tw-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.shadow-orange-500\/30 {
  --tw-shadow-color: rgb(249 115 22 / 0.3);
  --tw-shadow: var(--tw-shadow-colored);
}
.backdrop-blur-sm {
  --tw-backdrop-blur: blur(4px);
  -webkit-backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);
  backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);
}
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.transition-colors {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.transition-shadow {
  transition-property: box-shadow;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.duration-150 {
  transition-duration: 150ms;
}
.duration-200 {
  transition-duration: 200ms;
}
@keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
@keyframes modalZoom {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
.animate-fadeIn {
    animation: fadeIn 0.4s ease-out;
  }
.animate-modalZoom {
    animation: modalZoom 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
.hover\:bg-gray-100:hover {
  --tw-bg-opacity: 1;
  background-color: rgb(243 244 246 / var(--tw-bg-opacity, 1));
}
.hover\:bg-gray-200:hover {
  --tw-bg-opacity: 1;
  background-color: rgb(229 231 235 / var(--tw-bg-opacity, 1));
}
.hover\:bg-gray-50:hover {
  --tw-bg-opacity: 1;
  background-color: rgb(249 250 251 / var(--tw-bg-opacity, 1));
}
.hover\:from-orange-600:hover {
  --tw-gradient-from: #ea580c var(--tw-gradient-from-position);
  --tw-gradient-to: rgb(234 88 12 / 0) var(--tw-gradient-to-position);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.hover\:to-orange-700:hover {
  --tw-gradient-to: #c2410c var(--tw-gradient-to-position);
}
.hover\:text-gray-600:hover {
  --tw-text-opacity: 1;
  color: rgb(75 85 99 / var(--tw-text-opacity, 1));
}
.hover\:text-gray-700:hover {
  --tw-text-opacity: 1;
  color: rgb(55 65 81 / var(--tw-text-opacity, 1));
}
.hover\:text-orange-600:hover {
  --tw-text-opacity: 1;
  color: rgb(234 88 12 / var(--tw-text-opacity, 1));
}
.hover\:shadow-lg:hover {
  --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.hover\:shadow-md:hover {
  --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.hover\:shadow-xl:hover {
  --tw-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.hover\:shadow-orange-500\/40:hover {
  --tw-shadow-color: rgb(249 115 22 / 0.4);
  --tw-shadow: var(--tw-shadow-colored);
}
.focus\:border-transparent:focus {
  border-color: transparent;
}
.focus\:bg-white:focus {
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
}
.focus\:outline-none:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
}
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}
.focus\:ring-orange-500:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(249 115 22 / var(--tw-ring-opacity, 1));
}
.focus\:ring-offset-2:focus {
  --tw-ring-offset-width: 2px;
}
.disabled\:cursor-not-allowed:disabled {
  cursor: not-allowed;
}
.disabled\:opacity-50:disabled {
  opacity: 0.5;
}
.disabled\:shadow-none:disabled {
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.group:hover .group-hover\:text-gray-800 {
  --tw-text-opacity: 1;
  color: rgb(31 41 55 / var(--tw-text-opacity, 1));
}
@media (min-width: 640px) {

  .sm\:inline {
    display: inline;
  }

  .sm\:p-8 {
    padding: 2rem;
  }

  .sm\:px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }

  .sm\:py-6 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
  }

  .sm\:text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
  }

  .sm\:text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
  }

  .sm\:text-base {
    font-size: 1rem;
    line-height: 1.5rem;
  }
}

        div#logestay-forgot-modal[hidden], .logestay-language-dropdown.hidden{
            display: none;
        }
        .sso-overlay {
            display: none;
        }
        .logestay-language-dropdown {
            display: block;
        }
        .logestay-login-submit.loading {
  opacity: 0.7;
  pointer-events: none;
}
.logestay-brand img{
  max-width: 162px;
  height: auto;

}
    </style>
</head>
<body <?php body_class( 'logestay-login-body' ); ?>>

<?php wp_body_open(); ?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col logestay-login-page">
    <header class="w-full px-4 sm:px-6 py-4 sm:py-6 flex justify-between items-center logestay-login-header">
        <div class="flex items-center gap-3 logestay-brand">
            <!--<div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center shadow-md logestay-brand-icon">
                <span class="text-white font-bold text-lg">L</span>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 logestay-brand-title">
                Loge Stay
            </h1> -->
            <?php
$opts = get_option('logestay_settings');
$logo_id = !empty($opts['logestay_email_logo_id']) ? $opts['logestay_email_logo_id'] : '';

if ($logo_id) {
    echo wp_get_attachment_image($logo_id, 'full', false, [
        'class' => 'logestay-email-logo',
        'alt'   => get_bloginfo('name')
    ]);
}
?>
        </div>

        <?php
            if ( function_exists( 'pll_the_languages' ) ) :

                $langs = pll_the_languages( array(
                    'raw'                    => 1,
                    'hide_if_empty'          => 0,
                    'hide_if_no_translation' => 0,
                ) );

                if ( is_array( $langs ) && ! empty( $langs ) ) :

                    $flag_map = array(
                        'fr' => '🇫🇷',
                        'en' => '🇬🇧',
                        'pt' => '🇵🇹',
                        'es' => '🇪🇸',
                    );

                    $current = null;
                    foreach ( $langs as $lang ) {
                        if ( ! empty( $lang['current_lang'] ) ) {
                            $current = $lang;
                            break;
                        }
                    }

                    if ( ! $current ) {
                        $current = reset( $langs );
                    }

                    $current_slug = (string) ( $current['slug'] ?? '' );
                    $current_name = (string) ( $current['name'] ?? '' );
                    $current_flag = $flag_map[ $current_slug ] ?? '🌐';
                    ?>

                    <div class="relative logestay-language-selector logestay-lang">
                        <button
                            type="button"
                            class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 logestay-language-toggle logestay-lang-btn"
                            aria-label="<?php echo esc_attr__( 'Sélectionner une langue', 'logestay' ); ?>"
                            aria-expanded="false"
                        >
                            <span class="text-xl logestay-language-flag"><?php echo esc_html( $current_flag ); ?></span>
                            <span class="hidden sm:inline text-sm font-medium text-gray-700 logestay-language-name">
                                <?php echo esc_html( $current_name ); ?>
                            </span>
                            <span class="w-4 h-4 text-gray-500 transition-transform duration-200 logestay-language-chevron logestay-lang-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </span>
                        </button>

                        <div class="fixed inset-0 z-10 logestay-language-backdrop" hidden></div>

                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-20 logestay-language-dropdown logestay-lang-menu hidden" hidden>
                            <?php foreach ( $langs as $lang ) : ?>
                                <?php
                                $slug       = (string) ( $lang['slug'] ?? '' );
                                $name       = (string) ( $lang['name'] ?? '' );
                                $is_current = ! empty( $lang['current_lang'] );
                                $flag       = $flag_map[ $slug ] ?? '🌐';

                                /*
                                 * Build the login URL for each language.
                                 * pll_home_url($slug) gives the language home URL.
                                 * Then we append login/.
                                 */
                                $base_url = function_exists( 'pll_home_url' ) ? pll_home_url( $slug ) : home_url( '/' . $slug . '/' );
                                $url      = trailingslashit( $base_url ) . 'login/';

                                $item_class = $is_current
                                    ? 'w-full flex items-center gap-3 px-4 py-2.5 transition-colors duration-150 bg-orange-50 text-gray-900 font-semibold logestay-language-option'
                                    : 'w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150 text-gray-700 logestay-language-option';
                                ?>
                                <a
                                    href="<?php echo esc_url( $url ); ?>"
                                    class="<?php echo esc_attr( $item_class ); ?>"
                                    data-lang="<?php echo esc_attr( $slug ); ?>"
                                    hreflang="<?php echo esc_attr( $slug ); ?>"
                                    lang="<?php echo esc_attr( $slug ); ?>"
                                >
                                    <span class="text-xl"><?php echo esc_html( $flag ); ?></span>
                                    <span class="text-sm"><?php echo esc_html( $name ); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-8 logestay-login-main">
        <div class="w-full max-w-md animate-fadeIn logestay-login-card-wrap">
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100 logestay-login-card">
                <div class="flex flex-col items-center mb-8 logestay-login-card-head">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mb-4 shadow-sm logestay-login-head-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-8 h-8 text-orange-500"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path></svg>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 text-center logestay-login-title">
                        <?php esc_html_e( 'Sign in to Loge Stay', 'logestay' ); ?>
                    </h2>

                    <p class="text-gray-500 text-sm sm:text-base mt-2 text-center logestay-login-subtitle">
                        <?php esc_html_e( 'Access your management space', 'logestay' ); ?>
                    </p>
                </div>

                
                <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3 animate-fadeIn logestay-login-error hidden">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="text-sm text-red-700 logestay-login-error-text"></p>
                </div>

                <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>" class="space-y-5 logestay-login-form" id="logestay-login-form">
                    <div>
                        <label for="identifier" class="block text-sm font-medium text-gray-700 mb-2 logestay-label">
                            <?php esc_html_e( 'Username or email address', 'logestay' ); ?>
                        </label>

                        <input
                            id="identifier"
                            type="text"
                            name="log"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all duration-200 text-gray-800 placeholder-gray-400 logestay-input logestay-identifier"
                            placeholder="<?php echo esc_attr__( 'your@email.com', 'logestay' ); ?>"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2 logestay-label">
                            <?php esc_html_e( 'Password', 'logestay' ); ?>
                        </label>

                        <div class="relative logestay-password-field">
                            <input
                                id="password"
                                type="password"
                                name="pwd"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all duration-200 text-gray-800 placeholder-gray-400 pr-12 logestay-input logestay-password"
                                placeholder="<?php echo esc_attr__( '••••••••', 'logestay' ); ?>"
                                required
                                autocomplete="current-password"
                            >

                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-200 logestay-password-toggle"
                                aria-label="<?php echo esc_attr__( 'Afficher le mot de passe', 'logestay' ); ?>"
                                data-show-label="<?php echo esc_attr__( 'Afficher le mot de passe', 'logestay' ); ?>"
                                data-hide-label="<?php echo esc_attr__( 'Masquer le mot de passe', 'logestay' ); ?>"
                            >
                                <span class="logestay-eye-icon">
                                    <svg class="w-5 h-5 icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="w-5 h-5 icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none;">
                                        <path d="m15 18-.722-3.25"></path>
                                        <path d="M2 8a10.645 10.645 0 0 0 20 0"></path>
                                        <path d="m20 15-1.726-2.05"></path>
                                        <path d="m4 15 1.726-2.05"></path>
                                        <path d="m9 18 .722-3.25"></path>
                                    </svg>
                                </span>
                            </button>

                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm logestay-login-options">
                        <label class="flex items-center gap-2 cursor-pointer group logestay-remember-wrap">
                            <input
                                type="checkbox"
                                name="rememberme"
                                value="forever"
                                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-2 focus:ring-orange-500 cursor-pointer logestay-remember"
                            >
                            <span class="text-gray-600 group-hover:text-gray-800 transition-colors duration-200">
                                <?php esc_html_e( 'Remember me', 'logestay' ); ?>
                            </span>
                        </label>

                        <button
                            type="button"
                            class="text-orange-500 hover:text-orange-600 font-medium transition-colors duration-200 logestay-forgot-trigger"
                        >
                            <?php esc_html_e( 'Forgot password?', 'logestay' ); ?>
                        </button>
                    </div>

                    <?php wp_nonce_field( 'logestay_login_action', 'logestay_login_nonce' ); ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url( admin_url() ); ?>">

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3.5 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 logestay-login-submit"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in w-5 h-5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" x2="3" y1="12" y2="12"></line></svg>
                        <span><?php esc_html_e( 'Sign in', 'logestay' ); ?></span>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 space-y-4 logestay-login-footer-links">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">
                            <?php esc_html_e( 'Are you the site owner?', 'logestay' ); ?>
                        </p>

                        <a
                            href="<?php echo esc_url( $owner_access_url ); ?>"
                            class="text-sm text-orange-500 hover:text-orange-600 font-medium transition-colors duration-200 inline-flex items-center gap-1 logestay-owner-link"
                        >
                            <?php esc_html_e( "Configure area access", 'logestay' ); ?>
                            <span class="text-lg">→</span>
                        </a>
                    </div>

                    <div class="text-center">
                        <a
                            href="<?php echo esc_url( $support_url ); ?>"
                            class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200 logestay-support-link"
                        >
                            <?php esc_html_e( 'Contact Loge Stay support', 'logestay' ); ?>
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6 logestay-login-bottom-text">
                <?php esc_html_e( 'Secure and encrypted connection', 'logestay' ); ?>
            </p>
        </div>
    </main>
</div>

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fadeIn logestay-forgot-modal" id="logestay-forgot-modal" hidden>
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm logestay-forgot-overlay" aria-hidden="true"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform animate-modalZoom overflow-hidden logestay-forgot-dialog" role="dialog" aria-modal="true" aria-labelledby="logestay-forgot-title">
        <button
            type="button"
            class="absolute top-4 right-4 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-gray-400 hover:text-gray-600 logestay-forgot-close"
            aria-label="<?php echo esc_attr__( 'Fermer la fenêtre', 'logestay' ); ?>"
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>

        <div class="p-8">
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center logestay-forgot-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key w-8 h-8 text-orange-500"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 text-center mb-3" id="logestay-forgot-title">
                <?php esc_html_e( 'Forgot your password?', 'logestay' ); ?>
            </h2>

            <p class="text-sm text-gray-500 text-center mb-8">
                <?php esc_html_e( 'Enter your email address to receive a reset link.', 'logestay' ); ?>
            </p>

            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3 animate-fadeIn logestay-forgot-success hidden mb-4">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5"></path>
                </svg>
                <p class="text-sm text-green-700">
                    <?php esc_html_e( 'A reset link has been sent to your email address.', 'logestay' ); ?>
                </p>
            </div>

            <form method="post" action="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="space-y-6 logestay-forgot-form" <?php echo $reset_sent ? 'hidden' : ''; ?>>
                <?php wp_nonce_field( 'logestay_forgot_action', 'logestay_forgot_nonce' ); ?>
                <div>
                    <label for="reset-email" class="block text-sm font-medium text-gray-700 mb-2">
                        <?php esc_html_e( 'Email address', 'logestay' ); ?>
                    </label>

                    <input
                        id="reset-email"
                        type="email"
                        name="user_login"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 logestay-forgot-email"
                        placeholder="<?php echo esc_attr__( 'example@email.com', 'logestay' ); ?>"
                        required
                    >
                </div>

                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3 animate-fadeIn logestay-forgot-error mt-4 hidden" >
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="text-sm text-red-700 logestay-forgot-error-text">
                        <?php echo ! empty( $lost_error ) ? esc_html( $lost_error ) : esc_html__( 'Une erreur est survenue. Veuillez réessayer.', 'logestay' ); ?>
                    </p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all duration-200 logestay-forgot-cancel"
                    >
                        <?php esc_html_e( 'Cancel', 'logestay' ); ?>
                    </button>

                    <button
                        type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-xl hover:from-orange-600 hover:to-orange-700 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2 logestay-forgot-submit"
                    >
                        <span class="logestay-forgot-submit-spinner" style="display:none;">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                            </svg>
                        </span>
                        <span class="logestay-forgot-submit-text"><?php esc_html_e( 'Send reset link', 'logestay' ); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$logestay_open_forgot = ( $reset_sent || ! empty( $lost_error ) );
?>
<script>
window.logestayLoginConfig = {
    openForgotModal: <?php echo $logestay_open_forgot ? 'true' : 'false'; ?>
};
</script>

<?php
$reset_key   = isset($_GET['reset_key']) ? sanitize_text_field($_GET['reset_key']) : '';
$reset_login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

$is_reset_page = false;

if ($reset_key && $reset_login) {
    $user = check_password_reset_key($reset_key, $reset_login);

    if (!is_wp_error($user)) {
        $is_reset_page = true;
    }
}
?>

<?php if ($is_reset_page): ?>
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 logestay-reset-modal">

    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

        <h2 class="text-2xl font-bold text-center mb-6">
            <?php esc_html_e('Reset your password', 'logestay'); ?>
        </h2>

        <form class="space-y-5 logestay-reset-form">

            <?php wp_nonce_field('logestay_reset_action','logestay_reset_nonce'); ?>

            <input type="hidden" name="reset_key" value="<?php echo esc_attr($reset_key); ?>">
            <input type="hidden" name="login" value="<?php echo esc_attr($reset_login); ?>">

            <input type="password" name="password" placeholder="<?php esc_attr_e('New password','logestay'); ?>" class="w-full px-4 py-3 border rounded-xl" required>

            <input type="password" name="confirm_password" placeholder="<?php esc_attr_e('Confirm password','logestay'); ?>" class="w-full px-4 py-3 border rounded-xl" required>

            <div class="logestay-reset-error text-red-600 text-sm hidden"></div>

            <div class="logestay-reset-success text-green-600 text-sm hidden">
                <?php esc_html_e('Password updated successfully', 'logestay'); ?>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3.5 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <?php esc_html_e('Update password', 'logestay'); ?>
            </button>

        </form>

    </div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>