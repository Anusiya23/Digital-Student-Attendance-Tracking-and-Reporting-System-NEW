

$(document).ready(function() {
    $('#class_name').change(function() {
        var className = $(this).val();
        if (className !== '') {
            $.post('get_cls_dept.php', {class_name: className}, function(data) {
                $('#department').val(data);
            });
        }
    });

    $('#department').change(function() {
        var department = $(this).val();
        if (department !== '') {
            $.post('get_cls_dept.php', {department: department}, function(data) {
                $('#class_name').html(data);
            });
        }
    });
});
