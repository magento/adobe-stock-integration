define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {

    return function (config, element) {
        // element = element.cloneNode(true);
        // var div = document.createElement('div');
        // div.append(element);
        // $(div).modal.html(div);
        // var modal = $(div.children[0]).modal({type: 'slide'});
        // modal.applyBindings();
        $(element).modal({type: 'slide'}).applyBindings();
    }
});