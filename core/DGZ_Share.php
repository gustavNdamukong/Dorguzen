<?php

namespace Dorguzen\Core;

/**
 * DGZ_Share — Social media share buttons widget.
 *
 * Renders a row of icon buttons that open native platform share dialogs.
 * Zero external dependencies — pure HTML/CSS/JS, SVG icons inline.
 * Styles and the copy-to-clipboard script are injected once per page
 * via a static flag, so calling shareButtons() multiple times on the
 * same page is safe.
 *
 * Quick start:
 *   <?= shareButtons($url, $title) ?>
 *
 * Or call the class directly:
 *   <?= DGZ_Share::buttons($url, $title) ?>
 *
 * Options array keys:
 *   'platforms' => array   Which platforms to show (default: all).
 *                          Valid values: 'facebook', 'whatsapp', 'twitter', 'email', 'copy'
 *   'label'     => string  Text shown before the buttons. Default: 'Share:'. Set '' to hide.
 *   'size'      => int     Button diameter in px. Default: 38.
 *   'class'     => string  Extra CSS class(es) added to the wrapper div.
 *
 * Supported platforms and their share mechanism:
 *   facebook  — https://www.facebook.com/sharer/sharer.php?u={url}
 *   whatsapp  — https://wa.me/?text={title}+{url}
 *   twitter   — https://twitter.com/intent/tweet?url={url}&text={title}
 *   email     — mailto:?subject={title}&body={url}
 *   copy      — copies URL to clipboard via navigator.clipboard (JS fallback included)
 *
 * Note on TikTok: TikTok has no web-based share URL (it is a video creation
 * platform, not a link-sharing service). Use the 'copy' button instead —
 * users can paste the copied link into any TikTok caption or bio.
 */
class DGZ_Share
{
    /** @var bool Tracks whether CSS + JS assets have been emitted this request */
    private static bool $assetsEmitted = false;

    // ------------------------------------------------------------------
    //  SVG icon paths (viewBox="0 0 24 24", fill="currentColor")
    // ------------------------------------------------------------------
    private static array $svgs = [
        'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',

        'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',

        'twitter'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',

        'email'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',

        'copy'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>',
    ];

    // ------------------------------------------------------------------
    //  Share URL templates  (%s = encoded URL, second %s = encoded title)
    // ------------------------------------------------------------------
    private static array $shareUrls = [
        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=%s',
        'whatsapp' => 'https://wa.me/?text=%s%%20%s',   // title + space + url
        'twitter'  => 'https://twitter.com/intent/tweet?url=%s&text=%s',
        'email'    => 'mailto:?subject=%s&body=%s',
        'copy'     => '#',
    ];

    // ------------------------------------------------------------------
    //  Tooltip labels
    // ------------------------------------------------------------------
    private static array $labels = [
        'facebook' => 'Share on Facebook',
        'whatsapp' => 'Share on WhatsApp',
        'twitter'  => 'Share on X',
        'email'    => 'Share via Email',
        'copy'     => 'Copy link',
    ];

    // ------------------------------------------------------------------
    //  Brand hover colours
    // ------------------------------------------------------------------
    private static array $colors = [
        'facebook' => '#1877F2',
        'whatsapp' => '#25D366',
        'twitter'  => '#0f1419',
        'email'    => '#EA4335',
        'copy'     => '#6B7280',
    ];

    // ------------------------------------------------------------------
    //  Default platform order
    // ------------------------------------------------------------------
    private static array $defaultPlatforms = ['facebook', 'whatsapp', 'twitter', 'email', 'copy'];


    /**
     * Render share buttons HTML.
     *
     * @param string $url     The URL to share (should be the canonical page URL).
     * @param string $title   A short title or description for the shared content.
     * @param array  $options See class docblock for available keys.
     * @return string         Ready-to-echo HTML string.
     */
    public static function buttons(string $url, string $title = '', array $options = []): string
    {
        $platforms = $options['platforms'] ?? self::$defaultPlatforms;
        $label     = $options['label']     ?? 'Share:';
        $size      = (int) ($options['size'] ?? 38);
        $extraClass = isset($options['class']) ? ' ' . htmlspecialchars($options['class'], ENT_QUOTES) : '';

        $encUrl   = urlencode($url);
        $encTitle = urlencode($title);

        $html = self::emitAssets($size);

        $html .= '<div class="dgz-share' . $extraClass . '">';

        if ($label !== '') {
            $html .= '<span class="dgz-share__label">' . htmlspecialchars($label, ENT_QUOTES) . '</span>';
        }

        foreach ($platforms as $platform) {
            if (!isset(self::$svgs[$platform])) {
                continue;
            }

            $href = self::resolveHref($platform, $encUrl, $encTitle);
            $ariaLabel = htmlspecialchars(self::$labels[$platform], ENT_QUOTES);
            $tooltipText = htmlspecialchars(self::$labels[$platform], ENT_QUOTES);

            $targetAttr  = ($platform !== 'copy' && $platform !== 'email')
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';
            $copyAttr = $platform === 'copy'
                ? ' data-dgz-copy="' . htmlspecialchars($url, ENT_QUOTES) . '"'
                : '';

            $html .= '<a href="' . $href . '"'
                . ' class="dgz-share__btn dgz-share__btn--' . $platform . '"'
                . ' aria-label="' . $ariaLabel . '"'
                . $targetAttr
                . $copyAttr
                . '>';
            $html .= self::$svgs[$platform];
            $html .= '<span class="dgz-share__tooltip">' . $tooltipText . '</span>';
            $html .= '</a>';
        }

        $html .= '</div>';

        return $html;
    }


    // ------------------------------------------------------------------
    //  Private helpers
    // ------------------------------------------------------------------

    /**
     * Build the href for a given platform.
     */
    private static function resolveHref(string $platform, string $encUrl, string $encTitle): string
    {
        if ($platform === 'copy') {
            return '#';
        }

        $template = self::$shareUrls[$platform] ?? '#';

        return match ($platform) {
            'whatsapp' => sprintf($template, $encTitle, $encUrl),
            'twitter'  => sprintf($template, $encUrl, $encTitle),
            'email'    => sprintf($template, $encTitle, $encUrl),
            default    => sprintf($template, $encUrl),
        };
    }


    /**
     * Emit the CSS and JS assets once per page request.
     * Subsequent calls return an empty string.
     */
    private static function emitAssets(int $size): string
    {
        if (self::$assetsEmitted) {
            return '';
        }
        self::$assetsEmitted = true;

        // Build per-platform hover colour rules
        $hoverRules = '';
        foreach (self::$colors as $platform => $color) {
            $hoverRules .= ".dgz-share__btn--{$platform}:hover{background:{$color};color:#fff}";
        }

        $css = <<<CSS
<style>
.dgz-share{display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap;margin:12px 0}
.dgz-share__label{font-size:.8rem;color:#6b7280;font-weight:600;margin-right:2px;white-space:nowrap}
.dgz-share__btn{display:inline-flex;align-items:center;justify-content:center;width:{$size}px;height:{$size}px;border-radius:50%;background:#f3f4f6;color:#374151;text-decoration:none;position:relative;transition:background .18s,color .18s,transform .18s;flex-shrink:0}
.dgz-share__btn:hover{transform:scale(1.1)}
.dgz-share__btn svg{width:18px;height:18px;pointer-events:none}
{$hoverRules}
.dgz-share__tooltip{position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:.68rem;white-space:nowrap;padding:3px 8px;border-radius:4px;pointer-events:none;opacity:0;transition:opacity .15s}
.dgz-share__btn:hover .dgz-share__tooltip,.dgz-share__btn:focus .dgz-share__tooltip{opacity:1}
.dgz-share__btn--copy.dgz-copied .dgz-share__tooltip{opacity:1;background:#16a34a}
</style>
CSS;

        $js = <<<'JS'
<script>
(function(){
  document.addEventListener('click', function(e){
    var btn = e.target.closest('[data-dgz-copy]');
    if (!btn) return;
    e.preventDefault();
    var url = btn.getAttribute('data-dgz-copy');
    var tip = btn.querySelector('.dgz-share__tooltip');
    var orig = tip ? tip.textContent : '';
    function flash(msg){
      btn.classList.add('dgz-copied');
      if(tip) tip.textContent = msg;
      setTimeout(function(){ btn.classList.remove('dgz-copied'); if(tip) tip.textContent = orig; }, 2000);
    }
    if(navigator.clipboard && navigator.clipboard.writeText){
      navigator.clipboard.writeText(url).then(function(){ flash('Copied!'); }).catch(function(){ fallback(url); });
    } else { fallback(url); }
    function fallback(u){
      var ta = document.createElement('textarea');
      ta.value = u; ta.style.position='fixed'; ta.style.opacity='0';
      document.body.appendChild(ta); ta.focus(); ta.select();
      try{ document.execCommand('copy'); flash('Copied!'); } catch(err){ flash('Copy failed'); }
      document.body.removeChild(ta);
    }
  });
})();
</script>
JS;

        return $css . $js;
    }
}
