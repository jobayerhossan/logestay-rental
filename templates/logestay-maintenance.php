<?php
if ( ! defined('ABSPATH') ) exit;

// Optional: allow preview with ?maintenance_preview=1 for admins
if ( is_user_logged_in() && current_user_can('manage_options') && isset($_GET['maintenance_preview']) ) {
  // allow preview
} elseif ( is_user_logged_in() ) {
  // logged-in users should never see maintenance screen
  wp_redirect(home_url('/'));
  exit;
}

$options = get_option('logestay_settings', []);
$get = function($k, $default='') use ($options) {
  return isset($options[$k]) && $options[$k] !== '' ? $options[$k] : $default;
};


$opts = get_option('logestay_settings', []);

$title    = $opts['logestay_maint_title']    ?? __('Site under maintenance', 'logestay');
$subtitle = $opts['logestay_maint_subtitle'] ?? __('We will be back very soon', 'logestay');
$message  = $opts['logestay_maintenance_message']  ?? __('This site is currently undergoing maintenance to improve your experience. This interruption is temporary.', 'logestay');
$note     = $opts['logestay_maintenance_note']     ?? __("🔧 Technical update in progress.\nThank you for your patience.", 'logestay');
$contact_title     = $opts['logestay_maint_contact_title']     ?? __("🔧 Technical update in progress.\nThank you for your patience.", 'logestay');
$footer   = $opts['logestay_maint_footer_note']   ?? __('Thank you for your understanding and see you soon.', 'logestay');

$whatsapp = $opts['logestay_contact_whatsapp'] ?? '';
$phone    = $opts['logestay_contact_phone']    ?? '';
$email    = $opts['logestay_contact_email']    ?? get_option('admin_email');
$logestay_credit    = $opts['logestay_credit']    ?? 'Site created with Loge Stay';

// Send 503 (done in redirect hook too, but safe here)
status_header(503);
header('Retry-After: 3600'); // 1 hour
nocache_headers();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <?php wp_head(); ?>
  <style>
    *,:before,:after{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }*,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}:before,:after{--tw-content: ""}html,:host{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dl,dd,h1,h2,h3,h4,h5,h6,hr,figure,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}ol,ul,menu{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}button,[role=button]{cursor:pointer}:disabled{cursor:default}img,svg,video,canvas,audio,iframe,embed,object{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.container{width:100%}@media (min-width: 640px){.container{max-width:640px}}@media (min-width: 768px){.container{max-width:768px}}@media (min-width: 1024px){.container{max-width:1024px}}@media (min-width: 1280px){.container{max-width:1280px}}@media (min-width: 1536px){.container{max-width:1536px}}.mx-auto{margin-left:auto;margin-right:auto}.mb-10{margin-bottom:2.5rem}.mb-6{margin-bottom:1.5rem}.mb-8{margin-bottom:2rem}.mt-10{margin-top:2.5rem}.mt-8{margin-top:2rem}.flex{display:flex}.inline-flex{display:inline-flex}.h-10{height:2.5rem}.h-14{height:3.5rem}.h-16{height:4rem}.h-20{height:5rem}.h-7{height:1.75rem}.min-h-screen{min-height:100vh}.w-10{width:2.5rem}.w-14{width:3.5rem}.w-20{width:5rem}.w-7{width:1.75rem}.w-auto{width:auto}.w-full{width:100%}.max-w-2xl{max-width:42rem}.max-w-xl{max-width:36rem}.flex-1{flex:1 1 0%}.flex-col{flex-direction:column}.flex-wrap{flex-wrap:wrap}.items-center{align-items:center}.justify-center{justify-content:center}.gap-3{gap:.75rem}.gap-6{gap:1.5rem}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse: 0;margin-top:calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem * var(--tw-space-y-reverse))}.whitespace-pre-line{white-space:pre-line}.rounded-2xl{border-radius:1rem}.rounded-3xl{border-radius:1.5rem}.rounded-full{border-radius:9999px}.rounded-xl{border-radius:.75rem}.border{border-width:1px}.border-t{border-top-width:1px}.border-gray-100{--tw-border-opacity: 1;border-color:rgb(243 244 246 / var(--tw-border-opacity, 1))}.bg-gray-50{--tw-bg-opacity: 1;background-color:rgb(249 250 251 / var(--tw-bg-opacity, 1))}.bg-white{--tw-bg-opacity: 1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.bg-white\/50{background-color:#ffffff80}.bg-gradient-to-br{background-image:linear-gradient(to bottom right,var(--tw-gradient-stops))}.from-gray-50{--tw-gradient-from: #f9fafb var(--tw-gradient-from-position);--tw-gradient-to: rgb(249 250 251 / 0) var(--tw-gradient-to-position);--tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)}.to-gray-100{--tw-gradient-to: #f3f4f6 var(--tw-gradient-to-position)}.p-4{padding:1rem}.p-6{padding:1.5rem}.p-8{padding:2rem}.px-4{padding-left:1rem;padding-right:1rem}.py-12{padding-top:3rem;padding-bottom:3rem}.py-6{padding-top:1.5rem;padding-bottom:1.5rem}.py-8{padding-top:2rem;padding-bottom:2rem}.pt-10{padding-top:2.5rem}.text-center{text-align:center}.text-4xl{font-size:2.25rem;line-height:2.5rem}.text-sm{font-size:.875rem;line-height:1.25rem}.text-xl{font-size:1.25rem;line-height:1.75rem}.text-xs{font-size:.75rem;line-height:1rem}.font-bold{font-weight:700}.font-light{font-weight:300}.font-medium{font-weight:500}.leading-relaxed{line-height:1.625}.tracking-tight{letter-spacing:-.025em}.text-gray-400{--tw-text-opacity: 1;color:rgb(156 163 175 / var(--tw-text-opacity, 1))}.text-gray-500{--tw-text-opacity: 1;color:rgb(107 114 128 / var(--tw-text-opacity, 1))}.text-gray-600{--tw-text-opacity: 1;color:rgb(75 85 99 / var(--tw-text-opacity, 1))}.text-gray-700{--tw-text-opacity: 1;color:rgb(55 65 81 / var(--tw-text-opacity, 1))}.text-gray-900{--tw-text-opacity: 1;color:rgb(17 24 39 / var(--tw-text-opacity, 1))}.shadow-xl{--tw-shadow: 0 20px 25px -5px rgb(0 0 0 / .1), 0 8px 10px -6px rgb(0 0 0 / .1);--tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)}.shadow-gray-200\/50{--tw-shadow-color: rgb(229 231 235 / .5);--tw-shadow: var(--tw-shadow-colored)}.backdrop-blur-sm{--tw-backdrop-blur: blur(4px);-webkit-backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.transition-transform{transition-property:transform;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.duration-200{transition-duration:.2s}.hover\:bg-gray-50:hover{--tw-bg-opacity: 1;background-color:rgb(249 250 251 / var(--tw-bg-opacity, 1))}.group:hover .group-hover\:scale-110{--tw-scale-x: 1.1;--tw-scale-y: 1.1;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}@media (min-width: 768px){.md\:h-20{height:5rem}.md\:p-12{padding:3rem}.md\:text-2xl{font-size:1.5rem;line-height:2rem}.md\:text-5xl{font-size:3rem;line-height:1}}
.sso-overlay {
    display: none;
}
  </style>
</head>
<body <?php body_class('logestay-maintenance'); ?>>

	<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col">

<header class="w-full py-8 bg-white/50 backdrop-blur-sm">
  <div class="container mx-auto px-4 text-center">
    <div class="inline-flex items-center justify-center">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logestay_logo5.png' ); ?>"
           alt="<?php esc_attr_e('Loge Stay', 'logestay'); ?>"
           class="h-16 md:h-20 w-auto">
    </div>
  </div>
</header>

<main class="flex-1 flex items-center justify-center px-4 py-12">
  <div class="w-full max-w-2xl">

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 md:p-12">

      <div class="flex justify-center mb-8">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center"
             style="background-color: rgba(249,115,22,.082);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench w-10 h-10" style="color: rgb(249, 115, 22);"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
        </div>
      </div>

      <div class="text-center space-y-6 mb-10">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
          <?php echo esc_html($title); ?>
        </h1>

        <h2 class="text-xl md:text-2xl text-gray-600 font-light">
          <?php echo esc_html($subtitle); ?>
        </h2>

        <p class="text-gray-600 leading-relaxed max-w-xl mx-auto">
          <?php echo esc_html($message); ?>
        </p>

        <div class="mt-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
          <p class="text-gray-700 leading-relaxed">
            <?php echo $note; ?>
          </p>
        </div>
      </div>

      <div class="border-t border-gray-100 pt-10 mt-10">
        <p class="text-center text-sm text-gray-500 mb-6">
          <?php echo $contact_title; ?>
        </p>

        <div class="flex flex-wrap items-center justify-center gap-6">

          <?php if ($whatsapp): ?>
          <a href="<?php echo esc_url($whatsapp); ?>" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gray-50 transition-all duration-200"><div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(249, 115, 22, 0.082);"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle w-7 h-7" style="color: rgb(249, 115, 22);"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg></div><span class="text-xs text-gray-600 font-medium">WhatsApp</span></a>
          <?php endif; ?>

          <?php if ($phone): ?>
            <a href="tel:<?php echo esc_attr($phone); ?>" class="group flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gray-50 transition-all duration-200"><div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(249, 115, 22, 0.082);"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone w-7 h-7" style="color: rgb(249, 115, 22);"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></div><span class="text-xs text-gray-600 font-medium">Téléphone</span></a>
          <?php endif; ?>

          <?php if ($email): ?>
            <a href="mailto:<?php echo esc_attr($email); ?>" class="group flex flex-col items-center gap-3 p-4 rounded-xl hover:bg-gray-50 transition-all duration-200"><div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(249, 115, 22, 0.082);"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-7 h-7" style="color: rgb(249, 115, 22);"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg></div><span class="text-xs text-gray-600 font-medium">Email</span></a>
          <?php endif; ?>

        </div>
      </div>

      <p class="text-center text-gray-600 mt-10 text-sm">
        <?php echo esc_html($footer); ?>
      </p>

    </div>
  </div>
</main>

<footer class="w-full py-6 text-center">
  <p class="text-xs text-gray-400 font-light">
    <?php echo $logestay_credit; ?>
  </p>
</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>