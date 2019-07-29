function showAlert(type, content) {
    var strong = '',
        className = '';
    if (type === 'error') {
        strong = 'Failed';
        className = 'danger';
    }
    else if (type === 'success') {
        strong = 'Success!';
        className = 'success';
    }
    else
        return;
    $('#alerts').empty().append(alertNodel(strong, className, content));
}

function alertNodel(strong, className, content) {
    return '<div class="alert alert-' + className + ' alert-dismissible fade show" role="alert" style="z-index: 9999999;">\n' +
        '    <strong>' + strong + '</strong> ' + content + '\n' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
        '    <span aria-hidden="true">&times;</span>\n' +
        '</button>\n' +
        '</div>'
}
