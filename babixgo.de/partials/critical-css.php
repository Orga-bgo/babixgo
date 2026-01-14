<style>
:root {
  --md-background: rgb(16 20 23);
  --md-on-surface: rgb(255 255 255);
  --md-surface-container: rgb(36 40 44);
  --md-on-surface-variant: rgb(183, 194, 208);
  --md-primary-fixed-dim: rgb(146 206 245);
  --md-outline-variant: rgb(138 145 151);
  --zoom-scale: 0.92;
  --space-section: calc(32px * var(--zoom-scale));
  --space-card: calc(16px * var(--zoom-scale));
  --padding-card: calc(20px * var(--zoom-scale));
  --box-padding-top: calc(28px * var(--zoom-scale));
  --box-padding-inline: calc(18px * var(--zoom-scale));
  --box-padding-bottom: calc(32px * var(--zoom-scale));
  --layout-max-width: calc(800px * var(--zoom-scale));
  --header-height: calc(90px * var(--zoom-scale));
  --bg: var(--md-background);
  --card: var(--md-surface-container);
  --text: var(--md-on-surface);
  --muted: var(--md-on-surface-variant);
  --primary: var(--md-primary-fixed-dim);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 100%; scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
.box { max-width: var(--layout-max-width); margin: 0 auto; padding: var(--box-padding-top) var(--box-padding-inline) var(--box-padding-bottom); }
.header { position: sticky; top: 0; z-index: 100; background: var(--bg); border-bottom: 1px solid var(--md-outline-variant); }
.header-inner { display: flex; justify-content: space-between; align-items: center; height: var(--header-height); max-width: var(--layout-max-width); margin: 0 auto; padding: 0 var(--box-padding-inline); }
.logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
.logo img { height: calc(42px * var(--zoom-scale)); width: auto; }
.section-card { background: var(--card); border-radius: 16px; padding: var(--padding-card); margin-bottom: var(--space-card); }
.welcome-title { font-family: 'Montserrat', sans-serif; font-size: 1.75rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem; text-align: center; }
.is-hidden { display: none !important; }
</style>
