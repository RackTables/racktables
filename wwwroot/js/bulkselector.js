$(function () {
    $("#selectableRack tbody").selectable({
        filter: 'td.atom',
        stop: function () {
            $(".ui-selected input:enabled", this).each(function () {
                this.checked = !this.checked
            });
        }
    });
});