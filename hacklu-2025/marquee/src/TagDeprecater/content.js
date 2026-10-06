chrome.storage.sync.get('replacementString', ({ replacementString }) => {
    // can be extended, from https://www.w3.org/TR/2014/REC-html5-20141028/obsolete.html#obsolete
    const deprecatedTags = [
        'applet',
        'acronym',
        'bgsound',
        'dir',
        'frame',
        'frameset',
        'noframes',
        'hgroup',
        'isindex',
        'listing',
        'nextid',
        'noembed',
        'plaintext',
        'strike',
        'xmp',
        'basefont',
        'big',
        'blink',
        'center',
        'font',
        'marquee',
        'multicol',
        'nobr',
        'spacer',
        'tt'
    ];

    deprecatedTags.forEach((tag) => {
        const elements = document.querySelectorAll(tag);
        elements.forEach((el) => {
            el.outerHTML = replacementString;
        });
    });
});