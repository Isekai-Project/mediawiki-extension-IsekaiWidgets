$(function() {
    $('.isekai-buttonlink').each(function() {
        var $this = $(this);

        var opt = {
            label: $this.text(),
            href: $this.attr('href'),
            target: $this.attr('target'),
        }

        if ($this.attr('data-framed') === 'true') {
            opt.framed = true;
        }
        if ($this.attr('data-flags')) {
            var flags = $this.attr('data-flags');
            if (flags) {
                opt.flags = flags.split(' ');
            }
        }

        var $button = new OO.ui.ButtonWidget(opt);
        
        $this.replaceWith($button.$element);
    });
});