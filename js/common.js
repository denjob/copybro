// PAGE

var common = {

    init: function() {
        console.log('you code here ...');
        add_class_delayed('header', 'startup', 700);
        add_class_delayed('hsub_1_up', 'startup', 900);
        add_class_delayed('hsub_1_down', 'startup', 1100);
        add_class_delayed('hsub_2_up', 'startup', 1300);
        add_class_delayed('hsub_2_down', 'startup', 1100);
    }

}

add_event(document, 'DOMContentLoaded', common.init);