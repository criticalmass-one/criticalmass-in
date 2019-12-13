import CookieNoticeJS from 'cookie-notice/dist/cookie.notice.min';

export default function() {
    new CookieNoticeJS({
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
}
