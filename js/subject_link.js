
$(document).ready(function () {
    $('#class_name').change(function () {
        let className = $(this).val();
        if (className != '') {
            $.post('get_subject.php', { class_name: className }, function (data) {
                $('#subject_name').html(data);
                $('#subject_papercode').val(''); // Clear previous paper code
            });
        }
    });

    $('#subject_name').change(function () {
        let subjectName = $(this).val();
        let className = $('#class_name').val();
        if (subjectName != '' && className != '') {
            $.post('get_subject.php', { subject_name: subjectName, class_name: className }, function (data) {
                $('#subject_papercode').val(data);
            });
        }
    });
});

