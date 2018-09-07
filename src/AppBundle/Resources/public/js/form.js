(function ($, window, document) {

    var dirty = false;

    $(document).ready(function () {
        var hostname = window.location.hostname.replace('www.', '');
        $('a').each(function (index, value) {
            if (value.hostname !== hostname) {
                $(this).attr('target', '_blank');
            }
        });

        $(window).bind('beforeunload', function (e) {
            if (dirty) {
                var message = 'You have unsaved changes.';
                e.returnValue = message;
                return message;
            }
        });

        $("*[data-confirm]").each(function () {
            var $this = $(this);
            $this.click(function () {
                return window.confirm($this.data('confirm'));
            });
        });

        $('form').each(function () {
            var $form = $(this);
            $form.on('input', function () {
                dirty = true;
            });
            $form.on('submit', function () {
                $(window).unbind('beforeunload');
            });
        });
    });

    function addFormItem($container) {
        var prototype = $container.data('prototype');
        var index = $container.data('count');
        var $form = $(prototype.replace(/__name__/g, index).replace(/label__/g, ''));
        $container.append($form);
        $form.children('label').replaceWith('<div class="col-sm-2"><a class="btn btn-primary remove">Remove</a></div>');
        $form.find("a.remove").click(function (e) {
            e.preventDefault();
            $form.remove();
        });
        $container.data('count', index + 1);
    }

    function updateFormItem($container) {
        $container.data('count', $container.find('div.form-group').length);
        $container.find('.form-group').each(function (index, element) {
            var $form = $(element);
            $form.find('label').replaceWith('<div class="col-sm-2"><a class="btn btn-primary remove">Remove</a></div>');
            $form.find("a.remove").click(function (e) {
                e.preventDefault();
                $form.remove();
            });
        });
    }

    $(document).ready(function () {
        $('input:file').change(function () {
            var $input = $(this);
            if ($input.data('maxsize') && $input.data('maxsize') < this.files[0].size) {
                $input.prop('files', new FileList());
                alert('The selected file is too big.');
            }
        });

        if (window.CKEDITOR) {
            for (var key in window.CKEDITOR.instances) {
                var instance = window.CKEDITOR.instances[key];
                instance.on('mode', function () {
                    if (this.mode == 'source') {
                        var editable = instance.editable();
                        editable.attachListener(editable, 'input', function () {
                            dirty = true;
                        });
                    }
                });
                instance.on('change', function () {
                    dirty = true;
                });
            }
        }

        $('form div.collection').each(function (idx, element) {
            var $e = $(element);
            $e.children("label").append('<a href="#" class="btn btn-primary">Add</a>');
            var $a = $e.find("a");
            var $container = $e.find('div[data-prototype]');
            updateFormItem($container);
            $a.click(function (e) {
                e.preventDefault();
                addFormItem($container);
            });
        });
    });

})(jQuery, window, document);
