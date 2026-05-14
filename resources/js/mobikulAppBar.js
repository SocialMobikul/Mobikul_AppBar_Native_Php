const baseUrl = '/_native/api/call';

const allowedIcons = new Set([
  'back',
  'menu',
  'search',
  'cart',
  'profile',
  'wishlist',
  'share',
  'filter',
  'notifications',
  'more'
]);

const defaultsByContext = {
  home: [
    { icon: 'menu', action: 'open_menu' },
    { icon: 'search', action: 'open_search' },
    { icon: 'cart', action: 'open_cart' }
  ],
  product: [
    { icon: 'back', action: 'go_back' },
    { icon: 'wishlist', action: 'add_to_wishlist' },
    { icon: 'share', action: 'share_product' }
  ],
  checkout: [
    { icon: 'back', action: 'go_back' },
    { icon: 'cart', action: 'open_cart' }
  ],
  profile: [
    { icon: 'back', action: 'go_back' },
    { icon: 'notifications', action: 'open_notifications' },
    { icon: 'more', action: 'open_more' }
  ],
  default: [
    { icon: 'menu', action: 'open_menu' },
    { icon: 'search', action: 'open_search' }
  ]
};

async function bridgeCall(method, params = {}) {
  const response = await fetch(baseUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ method, params })
  });

  return response.json();
}

function sanitizeIcons(icons = [], context = 'default') {
  const normalized = icons.filter((item) => {
    if (!item || typeof item !== 'object') {
      return false;
    }

    return allowedIcons.has(item.icon) && typeof item.action === 'string' && item.action.length > 0;
  });

  return normalized.length ? normalized : (defaultsByContext[context] || defaultsByContext.default);
}

export async function configure({
  title = 'App',
  icons = [],
  context = 'default',
  showAppLogo = false,
  isElevated = true,
  isLeadingEnable = false,
  isHomeEnable = false,
  isAppLogoForDarkmode = false,
  appLogoUrl = null,
  darkAppLogoUrl = null,
  placeHolderImage = null,
  appBarBackgroundColor = '#ffffff',
  titleColor = '#111827',
  titleFontSize = 18,
  titleFontWeight = '600',
  logoWidth = 32,
  logoHeight = 32
} = {}) {
  return bridgeCall('MobikulAppBar.Configure', {
    title,
    context,
    icons: sanitizeIcons(icons, context),
    options: {
      showAppLogo,
      isElevated,
      isLeadingEnable,
      isHomeEnable,
      isAppLogoForDarkmode,
      appLogoUrl,
      darkAppLogoUrl,
      placeHolderImage,
      appBarBackgroundColor,
      titleColor,
      titleFontSize,
      titleFontWeight,
      logoWidth,
      logoHeight
    }
  });
}

export async function reset() {
  return bridgeCall('MobikulAppBar.Reset', {});
}
