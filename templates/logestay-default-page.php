<?php
/**
 * LOGESTAY Default Site Page (Public)
 * Shown to non-logged-in visitors when enabled.
 */
if ( ! defined('ABSPATH') ) exit;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<style>
*,
:before,
:after {
  --tw-border-spacing-x: 0;
  --tw-border-spacing-y: 0;
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x: ;
  --tw-pan-y: ;
  --tw-pinch-zoom: ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position: ;
  --tw-gradient-via-position: ;
  --tw-gradient-to-position: ;
  --tw-ordinal: ;
  --tw-slashed-zero: ;
  --tw-numeric-figure: ;
  --tw-numeric-spacing: ;
  --tw-numeric-fraction: ;
  --tw-ring-inset: ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / .5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur: ;
  --tw-brightness: ;
  --tw-contrast: ;
  --tw-grayscale: ;
  --tw-hue-rotate: ;
  --tw-invert: ;
  --tw-saturate: ;
  --tw-sepia: ;
  --tw-drop-shadow: ;
  --tw-backdrop-blur: ;
  --tw-backdrop-brightness: ;
  --tw-backdrop-contrast: ;
  --tw-backdrop-grayscale: ;
  --tw-backdrop-hue-rotate: ;
  --tw-backdrop-invert: ;
  --tw-backdrop-opacity: ;
  --tw-backdrop-saturate: ;
  --tw-backdrop-sepia: ;
  --tw-contain-size: ;
  --tw-contain-layout: ;
  --tw-contain-paint: ;
  --tw-contain-style: 
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
  --tw-pan-x: ;
  --tw-pan-y: ;
  --tw-pinch-zoom: ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position: ;
  --tw-gradient-via-position: ;
  --tw-gradient-to-position: ;
  --tw-ordinal: ;
  --tw-slashed-zero: ;
  --tw-numeric-figure: ;
  --tw-numeric-spacing: ;
  --tw-numeric-fraction: ;
  --tw-ring-inset: ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / .5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur: ;
  --tw-brightness: ;
  --tw-contrast: ;
  --tw-grayscale: ;
  --tw-hue-rotate: ;
  --tw-invert: ;
  --tw-saturate: ;
  --tw-sepia: ;
  --tw-drop-shadow: ;
  --tw-backdrop-blur: ;
  --tw-backdrop-brightness: ;
  --tw-backdrop-contrast: ;
  --tw-backdrop-grayscale: ;
  --tw-backdrop-hue-rotate: ;
  --tw-backdrop-invert: ;
  --tw-backdrop-opacity: ;
  --tw-backdrop-saturate: ;
  --tw-backdrop-sepia: ;
  --tw-contain-size: ;
  --tw-contain-layout: ;
  --tw-contain-paint: ;
  --tw-contain-style: 
}
*,
:before,
:after {
  box-sizing:border-box;
  border-width:0;
  border-style:solid;
  border-color:#e5e7eb
}
:before,
:after {
  --tw-content: ""
}
html,
:host {
  line-height:1.5;
  -webkit-text-size-adjust:100%;
  -moz-tab-size:4;
  -o-tab-size:4;
  tab-size:4;
  font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";
  font-feature-settings:normal;
  font-variation-settings:normal;
  -webkit-tap-highlight-color:transparent
}
body {
  margin:0;
  line-height:inherit
}
hr {
  height:0;
  color:inherit;
  border-top-width:1px
}
abbr:where([title]) {
  -webkit-text-decoration:underline dotted;
  text-decoration:underline dotted
}
h1,
h2,
h3,
h4,
h5,
h6 {
  font-size:inherit;
  font-weight:inherit
}
a {
  color:inherit;
  text-decoration:inherit
}
b,
strong {
  font-weight:bolder
}
code,
kbd,
samp,
pre {
  font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;
  font-feature-settings:normal;
  font-variation-settings:normal;
  font-size:1em
}
small {
  font-size:80%
}
sub,
sup {
  font-size:75%;
  line-height:0;
  position:relative;
  vertical-align:baseline
}
sub {
  bottom:-.25em
}
sup {
  top:-.5em
}
table {
  text-indent:0;
  border-color:inherit;
  border-collapse:collapse
}
button,
input,
optgroup,
select,
textarea {
  font-family:inherit;
  font-feature-settings:inherit;
  font-variation-settings:inherit;
  font-size:100%;
  font-weight:inherit;
  line-height:inherit;
  letter-spacing:inherit;
  color:inherit;
  margin:0;
  padding:0
}
button,
select {
  text-transform:none
}
button,
input:where([type=button]),
input:where([type=reset]),
input:where([type=submit]) {
  -webkit-appearance:button;
  background-color:transparent;
  background-image:none
}
:-moz-focusring {
  outline:auto
}
:-moz-ui-invalid {
  box-shadow:none
}
progress {
  vertical-align:baseline
}
::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
  height:auto
}
[type=search] {
  -webkit-appearance:textfield;
  outline-offset:-2px
}
::-webkit-search-decoration {
  -webkit-appearance:none
}
::-webkit-file-upload-button {
  -webkit-appearance:button;
  font:inherit
}
summary {
  display:list-item
}
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
  margin:0
}
fieldset {
  margin:0;
  padding:0
}
legend {
  padding:0
}
ol,
ul,
menu {
  list-style:none;
  margin:0;
  padding:0
}
dialog {
  padding:0
}
textarea {
  resize:vertical
}
input::-moz-placeholder,
textarea::-moz-placeholder {
  opacity:1;
  color:#9ca3af
}
input::placeholder,
textarea::placeholder {
  opacity:1;
  color:#9ca3af
}
button,
[role=button] {
  cursor:pointer
}
:disabled {
  cursor:default
}
img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display:block;
  vertical-align:middle
}
img,
video {
  max-width:100%;
  height:auto
}
[hidden]:where(:not([hidden=until-found])) {
  display:none
}
.mx-auto {
  margin-left:auto;
  margin-right:auto
}
.mb-2 {
  margin-bottom:.5rem
}
.mb-3 {
  margin-bottom:.75rem
}
.mb-6 {
  margin-bottom:1.5rem
}
.mb-8 {
  margin-bottom:2rem
}
.mt-10 {
  margin-top:2.5rem
}
.flex {
  display:flex
}
.h-12 {
  height:3rem
}
.h-16 {
  height:4rem
}
.h-8 {
  height:2rem
}
.min-h-screen {
  min-height:100vh
}
.w-16 {
  width:4rem
}
.w-8 {
  width:2rem
}
.w-auto {
  width:auto
}
.w-full {
  width:100%
}
.max-w-2xl {
  max-width:42rem
}
.max-w-xl {
  max-width:36rem
}
.flex-1 {
  flex:1 1 0%
}
.flex-col {
  flex-direction:column
}
.items-center {
  align-items:center
}
.justify-center {
  justify-content:center
}
.space-y-6>:not([hidden])~:not([hidden]) {
  --tw-space-y-reverse: 0;
  margin-top:calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
  margin-bottom:calc(1.5rem * var(--tw-space-y-reverse))
}
.rounded-2xl {
  border-radius:1rem
}
.rounded-full {
  border-radius:9999px
}
.rounded-lg {
  border-radius:.5rem
}
.border {
  border-width:1px
}
.border-t {
  border-top-width:1px
}
.border-gray-100 {
  --tw-border-opacity: 1;
  border-color:rgb(243 244 246 / var(--tw-border-opacity, 1))
}
.bg-gray-50 {
  --tw-bg-opacity: 1;
  background-color:rgb(249 250 251 / var(--tw-bg-opacity, 1))
}
.bg-orange-50 {
  --tw-bg-opacity: 1;
  background-color:rgb(255 247 237 / var(--tw-bg-opacity, 1))
}
.bg-orange-500 {
  --tw-bg-opacity: 1;
  background-color:rgb(249 115 22 / var(--tw-bg-opacity, 1))
}
.bg-white {
  --tw-bg-opacity: 1;
  background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))
}
.p-8 {
  padding:2rem
}
.px-4 {
  padding-left:1rem;
  padding-right:1rem
}
.px-8 {
  padding-left:2rem;
  padding-right:2rem
}
.py-3\.5 {
  padding-top:.875rem;
  padding-bottom:.875rem
}
.py-6 {
  padding-top:1.5rem;
  padding-bottom:1.5rem
}
.py-8 {
  padding-top:2rem;
  padding-bottom:2rem
}
.pb-20 {
  padding-bottom:5rem
}
.pb-6 {
  padding-bottom:1.5rem
}
.pt-6 {
  padding-top:1.5rem
}
.pt-8 {
  padding-top:2rem
}
.text-center {
  text-align:center
}
.text-3xl {
  font-size:1.875rem;
  line-height:2.25rem
}
.text-base {
  font-size:1rem;
  line-height:1.5rem
}
.text-sm {
  font-size:.875rem;
  line-height:1.25rem
}
.text-xl {
  font-size:1.25rem;
  line-height:1.75rem
}
.font-bold {
  font-weight:700
}
.font-light {
  font-weight:300
}
.font-medium {
  font-weight:500
}
.leading-relaxed {
  line-height:1.625
}
.text-gray-400 {
  --tw-text-opacity: 1;
  color:rgb(156 163 175 / var(--tw-text-opacity, 1))
}
.text-gray-500 {
  --tw-text-opacity: 1;
  color:rgb(107 114 128 / var(--tw-text-opacity, 1))
}
.text-gray-600 {
  --tw-text-opacity: 1;
  color:rgb(75 85 99 / var(--tw-text-opacity, 1))
}
.text-gray-700 {
  --tw-text-opacity: 1;
  color:rgb(55 65 81 / var(--tw-text-opacity, 1))
}
.text-gray-900 {
  --tw-text-opacity: 1;
  color:rgb(17 24 39 / var(--tw-text-opacity, 1))
}
.text-orange-500 {
  --tw-text-opacity: 1;
  color:rgb(249 115 22 / var(--tw-text-opacity, 1))
}
.text-white {
  --tw-text-opacity: 1;
  color:rgb(255 255 255 / var(--tw-text-opacity, 1))
}
.shadow-sm {
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}
.transition-colors {
  transition-property:color,background-color,border-color,text-decoration-color,fill,stroke;
  transition-timing-function:cubic-bezier(.4,0,.2,1);
  transition-duration:.15s
}
.duration-200 {
  transition-duration:.2s
}
.hover\:bg-orange-600:hover {
  --tw-bg-opacity: 1;
  background-color:rgb(234 88 12 / var(--tw-bg-opacity, 1))
}
.hover\:text-orange-600:hover {
  --tw-text-opacity: 1;
  color:rgb(234 88 12 / var(--tw-text-opacity, 1))
}
.hover\:shadow-md:hover {
  --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
  --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
  box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}
@media (min-width: 768px) {
  .md\:h-16 {
    height:4rem
  }
  .md\:w-auto {
    width:auto
  }
  .md\:p-12 {
    padding:3rem
  }
  .md\:text-2xl {
    font-size:1.5rem;
    line-height:2rem
  }
  .md\:text-4xl {
    font-size:2.25rem;
    line-height:2.5rem
  }
  .md\:text-lg {
    font-size:1.125rem;
    line-height:1.75rem
  }
}

	</style>
</head>
<body>
	
	<div class="min-h-screen bg-gray-50 flex flex-col">
   <header class="py-8 px-4">
      <div class="flex justify-center"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logestay_logo5.png" alt="Loge Stay" class="h-12 md:h-16 w-auto"></div>
   </header>
   <main class="flex-1 flex items-center justify-center px-4 pb-20">
      <div class="w-full max-w-2xl">
         <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">
            <div class="flex justify-center mb-8">
               <div class="w-16 h-16 rounded-full bg-orange-50 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield w-8 h-8 text-orange-500">
                     <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                  </svg>
               </div>
            </div>
            <div class="text-center space-y-6">
               <div>
                  <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Bienvenue sur Loge Stay</h2>
                  <h3 class="text-xl md:text-2xl text-gray-600 font-light">Ce site est en cours de configuration</h3>
               </div>
               <p class="text-gray-600 leading-relaxed max-w-xl mx-auto text-base md:text-lg">Le site Loge Stay est actuellement en phase de préparation. Le propriétaire finalise la configuration afin de vous proposer très prochainement une expérience de réservation fluide et agréable.</p>
               <p class="text-gray-600 leading-relaxed">Toutes les fonctionnalités seront disponibles sous peu.</p>
               <div class="pt-8 pb-6 border-t border-gray-100 mt-10">
                  <p class="text-gray-700 font-medium mb-2">Vous êtes le propriétaire du site ?</p>
                  <p class="text-gray-600 text-sm mb-6">Connectez-vous pour terminer la configuration et activer votre site.</p>
                  <a href="<?php echo esc_url( home_url('/login') ); ?>"
					   class="w-full md:w-auto px-8 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
					  <?php esc_html_e("Access configuration area", "logestay"); ?>
					</a>
               </div>
               <div class="pt-6">
                  <p class="text-sm text-gray-500 mb-2">Besoin d'assistance ? Notre équipe est à votre disposition.</p>
                  <a href="mailto:support@logestay.com" class="text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors duration-200">Contacter le support Loge Stay</a>
               </div>
            </div>
         </div>
      </div>
   </main>
   <footer class="py-6 px-4 text-center">
      <p class="text-sm text-gray-400">LOGESTAY — Plateforme premium de location courte durée</p>
   </footer>
</div>



	<?php wp_footer(); ?>
</body>
</html>
<?php