/**
 * Created by Ganzi on 2017/5/5.
 */
function insert_editor(elem, menu) {
    var editor = new xeditor(elem);
    editor.config.menus = [
        "undo",
        "redo",
        "|",
        'bold',
        'underline',
        'italic',
        'strikethrough',
        'eraser',
        'forecolor',
        'bgcolor',
        '|',
        'head',
        'unorderlist',
        'orderlist',
        'alignleft',
        'aligncenter',
        'alignright',
        '|',
        'link',
        'unlink',
        '|',
        'img'
    ];
    if (menu != null) {
        var menus = menu.split(',');
        $.each(menus, function (i, val) {
            editor.config.menus.remove(val);
        });
    }
    editor.create();
}

Array.prototype.remove = function (val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};