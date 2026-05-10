// CustomerNewTicket – inject envelope button on customer profile pages.

function cntBuildUrl(base, email) {
    base = base
        .replace(/([?&])x_embed=[^&]*/g, '$1')
        .replace(/[?&]{2,}/g, '?')
        .replace(/[?&]$/, '');
    if (!email) return base;
    var sep = (base.indexOf('?') !== -1) ? '&' : '?';
    return base + sep + 'to=' + encodeURIComponent(email);
}

function cntInitProfileButton() {
    var cnt = window.CustomerNewTicket;
    if (!cnt || !cnt.mailboxes || !cnt.mailboxes.length) return;

    $(function () {
        var email     = cnt.email || '';
        var label     = cnt.label || 'New Ticket';
        var mailboxes = cnt.mailboxes;

        // Inject inside .customer-profile-menu, before the cog toggle.
        // This keeps our icon in the same positioned container so vertical
        // alignment is automatic.
        var $cog = $('.customer-profile-menu > .dropdown-toggle:first');
        if (!$cog.length) return;

        var $el;

        if (mailboxes.length === 1) {
            var url = cntBuildUrl(mailboxes[0].url, email);
            $el = $('<a>')
                .addClass('glyphicon glyphicon-envelope link-grey cnt-envelope-btn')
                .attr('href', url)
                .attr('title', label)
                .on('click', function (e) {
                    e.preventDefault();
                    window.top.location.href = url;
                });
        } else {
            var $toggle = $('<a>')
                .addClass('glyphicon glyphicon-envelope link-grey dropdown-toggle cnt-envelope-btn')
                .attr('href', '#')
                .attr('data-toggle', 'dropdown')
                .attr('title', label);

            var $ul = $('<ul>').addClass('dropdown-menu dropdown-menu-right');
            $.each(mailboxes, function (i, mb) {
                var mbUrl = cntBuildUrl(mb.url, email);
                $('<li>').append(
                    $('<a>').attr('href', mbUrl).text(mb.name)
                        .on('click', function (e) {
                            e.preventDefault();
                            window.top.location.href = mbUrl;
                        })
                ).appendTo($ul);
            });

            // Wrap in its own .dropdown so Bootstrap toggles open on this
            // wrapper, not on the outer .customer-profile-menu
            $el = $('<div class="dropdown cnt-envelope-wrapper">').append($toggle, $ul);
        }

        $cog.before($el);
    });
}
