import cookieNoticeJS from 'cookie-notice/dist/cookie.notice.min';

let CookieNoticeJS = new cookieNoticeJS({
    'cookieNoticePosition': 'bottom',
    'learnMoreLinkEnabled': true,
    'learnMoreLinkHref': '/content/privacy',
    'expiresIn': 30,
    'buttonBgColor': '#204d74',
    'buttonTextColor': '#fff',
    'noticeBgColor': '#000',
    'noticeTextColor': '#fff',
    'linkColor': '#fff',
    'buttonLocales': {
        'de': 'Einverstanden'
    },
    'messageLocales': {
        'de': 'Diese Webseite setzt Cookies ein, damit eine vernünftige Bedienbarkeit gewährleistet ist.'
    }
});

export default CookieNoticeJS;
